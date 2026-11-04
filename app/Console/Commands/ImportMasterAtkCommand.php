<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Item;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/* REMARK
 * Tujuan       : Memasukkan master barang dari database/data/master-atk.csv (nama, satuan, kategori). Dicek per NAMA + KATEGORI (tanpa beda huruf besar/kecil dan spasi ganda; nama sama di kategori lain dianggap barang lain): yang sudah ada dilewati, yang belum ada ditambah. Hanya MENAMBAH, tidak mengubah / menghapus data yang ada.
 *                Kategori dan satuan yang belum ada dibuat. Tanpa --apply hanya menampilkan rencana (tidak menulis apa pun). Dengan --apply semuanya dalam satu transaksi.
 * Output       : m_category, m_unit, m_item (+ batch tunggal lewat Item::register).
 * Dipakai oleh : php artisan sstock:import-master-atk [--apply].
 * Status       : Sekali jalan (impor Master ATK).
 */
class ImportMasterAtkCommand extends Command
{
    protected $signature = 'sstock:import-master-atk {--apply : benar-benar menulis ke database}';

    protected $description = 'Impor master barang dari CSV: lewati nama yang sudah ada, tambah yang belum (default: simulasi)';

    private const NEW_CATEGORIES = ['RT' => 'Rumah Tangga', 'KES' => 'Kesehatan'];

    private const NEW_UNITS = ['rim' => 'Rim', 'lbr' => 'Lembar', 'slop' => 'Slop'];

    public function handle(): int
    {
        $rows = array_map('str_getcsv', file(database_path('data/master-atk.csv'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        array_shift($rows);
        $norm = fn ($s) => mb_strtoupper(trim(preg_replace('/\s+/u', ' ', $s)));
        $existing = array_flip(Item::with('category:id,code')->get()->map(fn ($i) => $norm($i->name).'|'.$i->category->code)->all());
        $apply = (bool) $this->option('apply');

        $run = function () use ($rows, $norm, &$existing, $apply) {
            $cats = Category::pluck('id', 'code')->all();
            $units = array_flip(Unit::pluck('code')->all());
            $made = ['kategori' => 0, 'satuan' => 0, 'barang' => 0, 'dilewati' => 0];
            foreach (self::NEW_CATEGORIES as $code => $name) {
                if (! isset($cats[$code])) {
                    $cats[$code] = $apply ? Category::create(['code' => $code, 'name' => $name])->id : 0;
                    $made['kategori']++;
                }
            }
            foreach (self::NEW_UNITS as $code => $name) {
                if (! isset($units[$code])) {
                    $apply && Unit::create(['code' => $code, 'name' => $name, 'kind' => 'count']);
                    $units[$code] = 1;
                    $made['satuan']++;
                }
            }
            foreach ($rows as [$name, $unit, $cat]) {
                if (! isset($units[$unit], $cats[$cat]) || $name === '') {
                    throw new \RuntimeException("Baris tidak valid: $name / $unit / $cat");
                }
                if (isset($existing[$key = $norm($name).'|'.$cat])) {
                    $made['dilewati']++;

                    continue;
                }
                $existing[$key] = true;
                $apply && Item::register(['name' => $name, 'category_id' => $cats[$cat], 'unit' => $unit]);
                $made['barang']++;
            }

            return $made;
        };

        $made = $apply ? DB::transaction($run) : $run();
        $this->info(($apply ? 'DITERAPKAN' : 'SIMULASI (tidak ada yang ditulis)').': '.json_encode($made));

        return self::SUCCESS;
    }
}
