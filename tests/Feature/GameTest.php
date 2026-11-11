<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class GameTest extends TestCase
{
    public function test_the_pilah_game_is_available_without_authentication(): void
    {
        $response = $this->get(route('game.pilah'));

        $response->assertOk()
            ->assertSee('Misi Pilah')
            ->assertSee('Mulai Misi')
            ->assertSee('Limbah B3')
            ->assertSee('href="'.route('welcome').'"', false)
            ->assertSee('Kembali ke beranda');
    }

    public function test_authenticated_users_return_to_their_role_dashboard_from_the_game(): void
    {
        $this->actingAs(new User(['role' => User::ROLE_KURIR]));

        $this->get(route('game.pilah'))
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('Kembali ke dashboard')
            ->assertDontSee('Kembali ke beranda');
    }

    public function test_dashboard_redirects_users_to_their_role_dashboard(): void
    {
        $roleRoutes = [
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_PENGELOLA_TPS => 'pengelola.dashboard',
            User::ROLE_KURIR => 'kurir.dashboard',
            User::ROLE_PENGGUNA => 'pengguna.dashboard',
        ];

        foreach ($roleRoutes as $role => $routeName) {
            $this->actingAs(new User(['role' => $role]))
                ->get(route('dashboard'))
                ->assertRedirect(route($routeName));
        }
    }
}
