<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/* REMARK
 * Tujuan       : Menjaga impor Spare Part: simulasi tidak menulis, apply menambah barang SPR + satu dokumen Saldo Awal terkonfirmasi (stok sesuai Excel, barang bersaldo 0 tanpa stok), jalan ulang tidak menggandakan.
 * Dipakai oleh : php artisan test.
 * Status       : Aktif.
 */
class ImportSparePartTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_apply_and_rerun(): void
    {
        $cat = Category::firstOrCreate(['code' => 'SPR'], ['name' => 'Sparepart Maintenance']);
        User::factory()->create(['username' => 'admin']);

        $this->artisan('sstock:import-spare-part')->assertSuccessful();
        $this->assertSame(0, Item::count());

        $this->artisan('sstock:import-spare-part', ['--apply' => true])->assertSuccessful();
        $this->assertSame(54, Item::where('category_id', $cat->id)->count());
        $this->assertSame(141.0, (float) Batch::sum('stock_qty'));
        $this->assertSame(1, StockDocument::count());
        $this->assertSame('confirmed', StockDocument::first()->status);
        $this->assertSame(0.0, (float) Batch::whereHas('item', fn ($q) => $q->where('name', 'Potensio 220 K'))->value('stock_qty'));

        $this->artisan('sstock:import-spare-part', ['--apply' => true])->assertSuccessful();
        $this->assertSame(54, Item::count());
        $this->assertSame(1, StockDocument::count());
    }
}
