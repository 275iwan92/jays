<?php

namespace App\Console\Commands;

use App\Enums\StockDocumentType;
use App\Models\Category;
use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use App\Services\StockDocuments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/* REMARK
 * Tujuan       : Memasukkan master barang kategori SPR (Sparepart Maintenance) dari database/data/spare-part.csv (nama, satuan, saldo awal) beserta dokumen Saldo Awal (harga 0, tanggal hari ini) yang langsung di-Confirm.
 *                Dicek per NAMA (tanpa beda huruf besar/kecil dan spasi ganda) di kategori SPR: yang sudah ada dilewati. Barang bersaldo 0 hanya masuk master. Hanya MENAMBAH.
 *                Tanpa --apply hanya simulasi (tidak menulis apa pun). Dengan --apply semuanya dalam satu transaksi.
 * Output       : m_item, i_batch, dokumen OPENING + pergerakan stok.
 * Dipakai oleh : php artisan sstock:import-spare-part [--apply].
 * Status       : Sekali jalan.
 */
class ImportSparePartCommand extends Command
{
    protected $signature = 'sstock:import-spare-part {--apply : benar-benar menulis ke database}';

    protected $description = 'Impor master spare part + saldo awal dari CSV (default: simulasi)';

    public function handle(StockDocuments $docs): int
    {
        $rows = array_map('str_getcsv', file(database_path('data/spare-part.csv'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        array_shift($rows);
        $norm = fn ($s) => mb_strtoupper(trim(preg_replace('/\s+/u', ' ', $s)));
        $cat = Category::where('code', 'SPR')->first();
        if (! $cat) {
            $this->error('Kategori SPR tidak ada.');

            return self::FAILURE;
        }
        $units = array_flip(Unit::pluck('code')->all());
        $existing = array_flip(Item::where('category_id', $cat->id)->pluck('name')->map($norm)->all());
        $user = User::where('username', 'admin')->first() ?? User::query()->firstOrFail();
        $apply = (bool) $this->option('apply');
        $made = ['barang' => 0, 'dilewati' => 0, 'baris_saldo' => 0, 'total_qty' => 0];

        $run = function () use ($rows, $norm, $cat, $units, &$existing, $apply, $docs, $user, &$made) {
            $lines = [];
            foreach ($rows as [$name, $unit, $qty]) {
                if ($name === '' || ! isset($units[$unit]) || ! is_numeric($qty) || $qty < 0) {
                    throw new \RuntimeException("Baris tidak valid: $name / $unit / $qty");
                }
                if (isset($existing[$norm($name)])) {
                    $made['dilewati']++;

                    continue;
                }
                $existing[$norm($name)] = true;
                $made['barang']++;
                $item = $apply ? Item::register(['name' => $name, 'category_id' => $cat->id, 'unit' => $unit]) : null;
                if ($qty > 0) {
                    $made['baris_saldo']++;
                    $made['total_qty'] += $qty;
                    $item && $lines[] = ['batch_id' => $item->batchFor(null)->id, 'qty' => $qty, 'unit_cost' => 0];
                }
            }
            if ($apply && $lines) {
                $doc = $docs->create(StockDocumentType::Opening, ['doc_at' => today(), 'note' => 'Saldo awal spare part (impor Excel)'], $lines, $user);
                $docs->confirm($doc, $user);
                $this->line('Dokumen: '.$doc->doc_no);
            }
        };

        $apply ? DB::transaction($run) : $run();
        $this->info(($apply ? 'DITERAPKAN' : 'SIMULASI (tidak ada yang ditulis)').': '.json_encode($made));

        return self::SUCCESS;
    }
}
