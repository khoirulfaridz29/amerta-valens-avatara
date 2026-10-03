<?php

namespace Tests\Feature;

use App\Enums\KasJenis;
use App\Enums\Role;
use App\Models\Proyek;
use App\Models\TransaksiKas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasTest extends TestCase
{
    use RefreshDatabase;

    private function bos(): User
    {
        return User::factory()->create(['role' => Role::BOS, 'is_active' => true]);
    }

    public function test_bos_dapat_menyimpan_transaksi_kas(): void
    {
        $this->actingAs($this->bos())
            ->post(route('kas.store'), [
                'tanggal' => now()->toDateString(),
                'jenis' => 'masuk',
                'nominal' => 1500000,
                'keterangan' => 'Pembayaran sewa',
            ])
            ->assertRedirect(route('kas.index'));

        $this->assertDatabaseHas('transaksi_kas', ['nominal' => 1500000, 'jenis' => 'masuk']);
    }

    public function test_nominal_nol_ditolak(): void
    {
        $this->actingAs($this->bos())
            ->post(route('kas.store'), ['tanggal' => now()->toDateString(), 'jenis' => 'keluar', 'nominal' => 0, 'keterangan' => 'Nol'])
            ->assertSessionHasErrors('nominal');
    }

    public function test_ringkasan_saldo_dihitung_benar(): void
    {
        $bos = $this->bos();
        TransaksiKas::create(['tanggal' => now(), 'jenis' => KasJenis::MASUK, 'nominal' => 1000000, 'keterangan' => 'Sewa']);

        $proyek = Proyek::create(['nama' => 'Proyek A', 'anggaran' => 5000000]);
        TransaksiKas::create(['proyek_id' => $proyek->id, 'tanggal' => now(), 'jenis' => KasJenis::KELUAR, 'nominal' => 250000, 'keterangan' => 'Solar']);

        $this->actingAs($bos)->get(route('kas.index'))->assertOk()->assertSee('Rp 750.000');
        $this->assertSame(250000.0, (float) TransaksiKas::where('proyek_id', $proyek->id)->where('jenis', KasJenis::KELUAR->value)->sum('nominal'));
    }
}
