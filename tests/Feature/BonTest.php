<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Bon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonTest extends TestCase
{
    use RefreshDatabase;

    private function bos(): User
    {
        return User::factory()->create(['role' => Role::BOS, 'is_active' => true]);
    }

    public function test_bos_dapat_membuat_bon(): void
    {
        $this->actingAs($this->bos())
            ->post(route('bon.store'), [
                'customer' => 'PT ABC',
                'tanggal' => now()->toDateString(),
                'total' => 10000000,
            ])
            ->assertRedirect(route('bon.index'));

        $this->assertDatabaseHas('bon', ['customer' => 'PT ABC']);
    }

    public function test_sisa_bon_berkurang_saat_kas_masuk_ditautkan(): void
    {
        $bos = $this->bos();
        $bon = Bon::create([
            'nomor' => 'BON-'.now()->year.'-0001',
            'customer' => 'PT ABC',
            'tanggal' => now()->toDateString(),
            'total' => 10000000,
        ]);

        $this->assertSame(10000000.0, $bon->sisa);

        $this->actingAs($bos)
            ->post(route('kas.store'), [
                'tanggal' => now()->toDateString(),
                'jenis' => 'masuk',
                'nominal' => 4000000,
                'keterangan' => 'Cicilan pertama',
                'bon_id' => $bon->id,
            ])
            ->assertRedirect(route('kas.index'));

        $bon->refresh();
        $this->assertSame(4000000.0, $bon->dibayar);
        $this->assertSame(6000000.0, $bon->sisa);
        $this->assertFalse($bon->lunas);
    }

    public function test_bon_lunas_bila_dibayar_penuh(): void
    {
        $bos = $this->bos();
        $bon = Bon::create([
            'nomor' => 'BON-'.now()->year.'-0002',
            'customer' => 'PT XYZ',
            'tanggal' => now()->toDateString(),
            'total' => 5000000,
        ]);

        $this->actingAs($bos)->post(route('kas.store'), [
            'tanggal' => now()->toDateString(), 'jenis' => 'masuk', 'nominal' => 5000000,
            'keterangan' => 'Pelunasan', 'bon_id' => $bon->id,
        ])->assertRedirect(route('kas.index'));

        $this->assertTrue($bon->fresh()->lunas);
    }

    public function test_operator_tidak_dapat_mengakses_bon(): void
    {
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true]);

        $this->actingAs($operator)->get(route('bon.index'))->assertForbidden();
    }
}
