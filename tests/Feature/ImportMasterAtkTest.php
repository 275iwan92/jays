<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/* REMARK
 * Tujuan       : Menjaga impor Master ATK: simulasi tidak menulis, apply hanya menambah, nama yang sudah ada dilewati (data lama tidak berubah), jalan ulang tidak menggandakan.
 * Dipakai oleh : php artisan test.
 * Status       : Aktif.
 */
class ImportMasterAtkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_apply_and_rerun(): void
    {
        $cat = Category::firstOrCreate(['code' => 'ATK'], ['name' => 'ATK']);
        $old = Item::register(['name' => 'box  file', 'category_id' => $cat->id, 'unit' => 'pcs', 'unit_price' => 5000]);

        $this->artisan('sstock:import-master-atk')->assertSuccessful();
        $this->assertSame(1, Item::count());

        $this->artisan('sstock:import-master-atk', ['--apply' => true])->assertSuccessful();
        $this->assertSame(127, Item::count());  // 127 baris Excel; "BOX FILE" lama dilewati, TALI RAFIA ATK dan RT dua barang
        $this->assertSame(1, Item::whereRaw('upper(name) like ?', ['BOX%FILE'])->count());
        $this->assertSame(5000.0, $old->fresh()->unit_price);
        $this->assertSame(Item::count(), Batch::count());
        $this->assertNotNull(Category::where('code', 'RT')->first());

        $this->artisan('sstock:import-master-atk', ['--apply' => true])->assertSuccessful();
        $this->assertSame(127, Item::count());
    }
}
