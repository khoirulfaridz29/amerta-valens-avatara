<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Alat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProyekAlatTest extends TestCase
{
    use RefreshDatabase;

    private function bos(): User
    {
        return User::factory()->create(['role' => Role::BOS, 'is_active' => true]);
    }

    public function test_bos_dapat_membuat_proyek(): void
    {
        $this->actingAs($this->bos())
            ->post(route('proyek.store'), ['nama' => 'Proyek A', 'lokasi' => 'Lampung', 'anggaran' => 1000000])
            ->assertRedirect(route('proyek.index'));

        $this->assertDatabaseHas('proyek', ['nama' => 'Proyek A']);
    }

    public function test_proyek_tanpa_nama_ditolak(): void
    {
        $this->actingAs($this->bos())
            ->post(route('proyek.store'), ['nama' => ''])
            ->assertSessionHasErrors('nama');
    }

    public function test_bos_dapat_membuat_alat_dengan_kode_unik(): void
    {
        $this->actingAs($this->bos())
            ->post(route('alat.store'), ['nama' => 'CAT 320', 'kode' => 'EX-01', 'status' => 'aktif'])
            ->assertRedirect(route('alat.index'));

        $this->assertDatabaseHas('alat', ['kode' => 'EX-01']);

        $this->actingAs($this->bos())
            ->post(route('alat.store'), ['nama' => 'CAT 330', 'kode' => 'EX-01', 'status' => 'aktif'])
            ->assertSessionHasErrors('kode');
    }

    public function test_status_alat_dapat_diubah(): void
    {
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-09', 'status' => 'aktif', 'active' => true]);

        $this->actingAs($this->bos())
            ->put(route('alat.update', $alat), ['nama' => 'CAT 320', 'kode' => 'EX-09', 'status' => 'perbaikan'])
            ->assertRedirect(route('alat.index'));

        $this->assertSame('perbaikan', $alat->fresh()->status->value);
    }

    public function test_bos_dapat_menonaktifkan_alat(): void
    {
        $alat = Alat::create(['nama' => 'CAT 320', 'kode' => 'EX-10', 'status' => 'aktif', 'active' => true]);

        $this->actingAs($this->bos())
            ->put(route('alat.active', $alat), ['active' => 0])
            ->assertRedirect();

        $this->assertFalse($alat->fresh()->active);
    }
}
