<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTenantTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // run migrations
        $this->artisan('migrate');
    }

    public function test_tenant_update_does_not_delete_record()
    {
        $admin = AdminUser::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'super_admin',
        ]);

        $tenant = Tenant::create([
            'name' => 'Acme Co',
            'slug' => 'acme-'.str()->random(5),
            'email' => 'billing@acme.local',
            'plan' => 'free',
            'plan_status' => 'active',
            'status' => 'active',
        ]);

        // verify edit page layout does not nest forms
        $html = $this->actingAs($admin, 'admin')->get(route('admin.tenants.edit', $tenant))->getContent();

        // there should be two form tags but the destroy form should come after closing the update form
        $this->assertMatchesRegularExpression("#</form>\s*<form[^>]+route\('admin.tenants.destroy'#", $html);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.tenants.update', $tenant), [
                'name' => 'Acme Corporation',
                'email' => 'billing+test@acme.local',
                'plan' => 'starter',
                'plan_status' => 'active',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.tenants.show', $tenant));

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Acme Corporation',
            'email' => 'billing+test@acme.local',
            'deleted_at' => null,
        ]);

        // ensure tenant still exists when re-querying
        $this->assertNotNull(Tenant::find($tenant->id));
    }
}
