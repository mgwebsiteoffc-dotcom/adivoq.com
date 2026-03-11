<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // make sure migrations run
        Artisan::call('migrate');
    }

    public function test_admin_can_view_and_update_system_settings()
    {
        // create a super admin user
        $admin = AdminUser::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'super_admin',
        ]);

        // visit settings page (should show defaults)
        $response = $this->actingAs($admin, 'admin')->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Site Name');

        // submit new values
        $payload = [
            'site_name' => 'My New Site',
            'default_currency' => 'USD',
            'default_gst_rate' => '18',
            'maintenance_mode' => '1',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => '587',
            'smtp_username' => 'user@example.com',
            'from_address' => 'noreply@example.com',
            'whatify_api_key' => 'abc123',
            'whatify_base_url' => 'https://whatify.test/api',
        ];

        $submit = $this->actingAs($admin, 'admin')
            ->followingRedirects()
            ->put(route('admin.settings.update'), $payload);

        $submit->assertStatus(200);
        $submit->assertSessionHas('success');

        // verify values stored in DB
        foreach ($payload as $key => $value) {
            if ($key === 'maintenance_mode') {
                $value = '1';
            }
            $this->assertDatabaseHas('system_settings', ['key' => $key, 'value' => $value]);
        }

        // config should have been updated
        $this->assertEquals('My New Site', config('app.name'));
        $this->assertEquals('USD', config('invoicehero.default_currency'));
        $this->assertEquals('18', config('invoicehero.default_gst_rate'));
        $this->assertEquals('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertEquals('abc123', config('services.whatify.api_key'));

        // maintenance mode should have been toggled
        $this->assertTrue(app()->isDownForMaintenance());

        // fetching page again should show updated values in inputs
        $view = $this->actingAs($admin, 'admin')->get(route('admin.settings.index'));
        $view->assertSee('value="My New Site"');
        $view->assertSee('value="smtp.example.com"');
    }
}
