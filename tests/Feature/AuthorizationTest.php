<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_cannot_access_admin_routes(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'guru']));

        $this->getJson('/api/admin/users')->assertForbidden();
    }

    public function test_ortu_cannot_access_guru_routes(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'ortu']));

        $this->getJson('/api/guru/dashboard')->assertForbidden();
    }
}
