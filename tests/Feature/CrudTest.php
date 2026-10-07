<?php

namespace Tests\Feature;

use App\Enums\AlatStatus;
use App\Enums\KasJenis;
use App\Enums\Role;
use App\Models\Alat;
use App\Models\LaporanHarian;
use App\Models\Proyek;
use App\Models\TransaksiKas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudTest extends TestCase
{
    use RefreshDatabase;

    private function bos(): User
    {
        return User::factory()->create(['role' => Role::BOS, 'is_active' => true]);
    }

    public function test_kas_dapat_diubah_dan_dihapus(): void
    {
        $bos = $this->bos();
        $kas = TransaksiKas::create(['tanggal' => now(), 'jenis' => KasJenis::MASUK, 'nominal' => 1000000, 'keterangan' => 'Awal']);

        $this->actingAs($bos)->put(route('kas.update', $kas), [
            'tanggal' => now()->toDateString(), 'jenis' => 'keluar', 'nominal' => 500000, 'keterangan' => 'Diubah',
        ])->assertRedirect(route('kas.index'));

        $this->assertDatabaseHas('transaksi_kas', ['id' => $kas->id, 'jenis' => 'keluar', 'keterangan' => 'Diubah']);

        $this->actingAs($bos)->delete(route('kas.destroy', $kas))->assertRedirect(route('kas.index'));
        $this->assertDatabaseMissing('transaksi_kas', ['id' => $kas->id]);
    }

    public function test_alat_tanpa_laporan_dapat_dihapus(): void
    {
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-01', 'status' => AlatStatus::AKTIF, 'active' => true]);

        $this->actingAs($this->bos())->delete(route('alat.destroy', $alat))->assertRedirect(route('alat.index'));
        $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
    }

    public function test_alat_dengan_laporan_tidak_dapat_dihapus(): void
    {
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-02', 'status' => AlatStatus::AKTIF, 'active' => true]);
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true]);
        LaporanHarian::create(['alat_id' => $alat->id, 'user_id' => $operator->id, 'tanggal' => now()->toDateString(), 'hm_awal' => 1, 'hm_akhir' => 2]);

        $this->actingAs($this->bos())->delete(route('alat.destroy', $alat))->assertRedirect();
        $this->assertDatabaseHas('alat', ['id' => $alat->id]);
    }

    public function test_kontrak_dengan_alat_tidak_dapat_dihapus(): void
    {
        $proyek = Proyek::create(['nama' => 'Proyek A', 'lokasi' => 'Lampung']);
        Alat::create(['proyek_id' => $proyek->id, 'nama' => 'CAT 320', 'kode' => 'EX-03', 'status' => AlatStatus::AKTIF, 'active' => true]);

        $this->actingAs($this->bos())->delete(route('proyek.destroy', $proyek))->assertRedirect();
        $this->assertDatabaseHas('proyek', ['id' => $proyek->id]);
    }

    public function test_operator_dengan_laporan_tidak_dapat_dihapus(): void
    {
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true]);
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-04', 'status' => AlatStatus::AKTIF, 'active' => true]);
        LaporanHarian::create(['alat_id' => $alat->id, 'user_id' => $operator->id, 'tanggal' => now()->toDateString(), 'hm_awal' => 1, 'hm_akhir' => 2]);

        $this->actingAs($this->bos())->delete(route('operator.destroy', $operator))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $operator->id]);
    }
}
