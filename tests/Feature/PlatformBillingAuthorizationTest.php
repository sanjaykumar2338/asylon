<?php

namespace Tests\Feature;

use App\Models\Org;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformBillingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_cannot_access_platform_billing(): void
    {
        $user = User::factory()->platformAdmin()->create();

        $this->actingAs($user)
            ->get('/platform/billing/subscriptions')
            ->assertForbidden();
    }

    public function test_org_admin_cannot_access_platform_billing(): void
    {
        $org = Org::factory()->create();
        $user = User::factory()->create([
            'role' => 'org_admin',
            'org_id' => $org->id,
        ]);

        $this->actingAs($user)
            ->get('/platform/billing/subscriptions')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_platform_billing(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)
            ->get('/platform/billing/subscriptions')
            ->assertOk();
    }

    public function test_inviting_platform_admin_ignores_org_assignment(): void
    {
        $acting = User::factory()->superAdmin()->create();
        $org = Org::factory()->create();

        $response = $this->actingAs($acting)->post(route('admin.users.store'), [
            'name' => 'Platform Support',
            'email' => 'platform@example.com',
            'role' => 'platform_admin',
            'org_id' => $org->id,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'platform@example.com',
            'role' => 'platform_admin',
            'org_id' => null,
        ]);
    }

    public function test_inviting_org_admin_requires_org(): void
    {
        $acting = User::factory()->superAdmin()->create();

        $response = $this->actingAs($acting)->from(route('admin.users.create'))->post(route('admin.users.store'), [
            'name' => 'Org Admin',
            'email' => 'orgadmin@example.com',
            'role' => 'org_admin',
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors('org_id');
    }
}
