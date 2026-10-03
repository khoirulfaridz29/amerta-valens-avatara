<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_login_dan_diarahkan_ke_dashboard(): void
    {
        $admin = User::factory()->create(['role' => Role::BOS, 'is_active' => true, 'email' => 'admin@test.local', 'password' => 'admin123']);

        $this->post('/login', ['email' => 'admin@test.local', 'password' => 'admin123'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_operator_login_diarahkan_ke_laporan(): void
    {
        User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true, 'email' => 'op@test.local', 'password' => 'operator123']);

        $this->post('/login', ['email' => 'op@test.local', 'password' => 'operator123'])->assertRedirect(route('laporan.index'));
    }

    public function test_password_salah_menghasilkan_error(): void
    {
        User::factory()->create(['role' => Role::BOS, 'email' => 'admin@test.local', 'password' => 'admin123']);

        $this->post('/login', ['email' => 'admin@test.local', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_dapat_login(): void
    {
        User::factory()->create(['role' => Role::OPERATOR, 'is_active' => false, 'email' => 'op@test.local', 'password' => 'operator123']);

        $this->post('/login', ['email' => 'op@test.local', 'password' => 'operator123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_operator_tidak_dapat_mengakses_halaman_admin(): void
    {
        $operator = User::factory()->create(['role' => Role::OPERATOR, 'is_active' => true]);

        $this->actingAs($operator)->get(route('proyek.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('kas.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('operator.index'))->assertForbidden();
        $this->actingAs($operator)->get(route('service.index'))->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_halaman_404_menampilkan_halaman_error_kustom(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan');
    }
}
