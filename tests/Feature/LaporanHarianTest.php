<?php

namespace Tests\Feature;

use App\Enums\AlatStatus;
use App\Enums\Role;
use App\Models\Alat;
use App\Models\LaporanHarian;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanHarianTest extends TestCase
{
    use RefreshDatabase;

    private function operator(): User
    {
        return User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true]);
    }

    public function test_operator_dapat_mengirim_laporan_dan_solar_dikonversi_ke_liter(): void
    {
        $operator = $this->operator();
        $proyek = Proyek::create(['nama' => 'Proyek A', 'lokasi' => 'Lampung']);
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-01', 'status' => AlatStatus::AKTIF, 'active' => true]);

        $this->actingAs($operator)
            ->post(route('laporan.store'), [
                'alat_id' => $alat->id,
                'proyek_id' => $proyek->id,
                'tanggal' => now()->toDateString(),
                'hm_awal' => 1200,
                'hm_akhir' => 1208.5,
                'solar_jerigen' => 3,
                'keterangan' => 'Gali parit',
            ])
            ->assertRedirect(route('laporan.index', ['tab' => 'riwayat']));

        $laporan = LaporanHarian::first();
        $this->assertNotNull($laporan);
        $this->assertSame($operator->id, $laporan->user_id);
        $this->assertSame('105.00', $laporan->solar_liter);
    }

    public function test_alat_dan_proyek_wajib_saat_membuat_laporan(): void
    {
        $this->actingAs($this->operator())
            ->post(route('laporan.store'), ['tanggal' => now()->toDateString(), 'hm_awal' => 1, 'hm_akhir' => 2])
            ->assertSessionHasErrors(['alat_id', 'proyek_id']);
    }

    public function test_operator_hanya_melihat_laporan_miliknya(): void
    {
        $operatorA = $this->operator();
        $operatorB = $this->operator();
        $alatA = Alat::create(['nama' => 'Excavator Alpha', 'kode' => 'A-1', 'status' => AlatStatus::AKTIF, 'active' => true]);
        $alatB = Alat::create(['nama' => 'Bulldozer Beta', 'kode' => 'B-1', 'status' => AlatStatus::AKTIF, 'active' => true]);

        LaporanHarian::create(['alat_id' => $alatA->id, 'user_id' => $operatorA->id, 'tanggal' => now()->toDateString(), 'hm_awal' => 1, 'hm_akhir' => 2, 'keterangan' => 'CATATAN-ALPHA']);
        LaporanHarian::create(['alat_id' => $alatB->id, 'user_id' => $operatorB->id, 'tanggal' => now()->toDateString(), 'hm_awal' => 1, 'hm_akhir' => 2, 'keterangan' => 'CATATAN-BETA']);

        $this->actingAs($operatorA)
            ->get(route('laporan.index', ['tab' => 'riwayat']))
            ->assertOk()
            ->assertSee('CATATAN-ALPHA')
            ->assertDontSee('CATATAN-BETA');
    }
}
