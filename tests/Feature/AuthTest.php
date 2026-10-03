<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_login_dengan_pin_dan_diarahkan_ke_dashboard(): void
    {
        $admin = User::factory()->create(['role' => Role::BOS, 'is_active' => true, 'pin' => Hash::make('123456')]);

        $this->post('/login', ['pin' => '123456'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_operator_login_dengan_pin_diarahkan_ke_laporan(): void
    {
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true, 'pin' => Hash::make('654321')]);

        $this->post('/login', ['pin' => '654321'])->assertRedirect(route('laporan.index'));
        $this->assertAuthenticatedAs($operator);
    }

    public function test_pin_salah_menghasilkan_error(): void
    {
        User::factory()->create(['role' => Role::BOS, 'pin' => Hash::make('123456')]);

        $this->post('/login', ['pin' => '000000'])->assertSessionHasErrors('pin');
        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_dapat_login(): void
    {
        User::factory()->create(['role' => Role::OPERATOR, 'is_active' => false, 'pin' => Hash::make('654321')]);

        $this->post('/login', ['pin' => '654321'])->assertSessionHasErrors('pin');
        $this->assertGuest();
    }

    public function test_operator_tidak_dapat_mengakses_halaman_admin(): void
    {
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true, 'pin' => Hash::make('654321')]);

        $this->actingAs($operator)->get(route('proyek.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('kas.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('operator.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('service.index'))->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
