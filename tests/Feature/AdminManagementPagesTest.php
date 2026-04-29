<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\University;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Services\System\SystemDataResetService;
use Tests\TestCase;

class AdminManagementPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_management_and_security_pages(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $routes = [
            'dashboard',
            'dashboard.users.index',
            'dashboard.universities.index',
            'dashboard.faculties.index',
            'dashboard.departments.index',
            'dashboard.roles.index',
            'dashboard.activity-logs.index',
            'dashboard.blocked-users.index',
            'dashboard.system.edit',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }

    public function test_non_admin_cannot_open_admin_users(): void
    {
        $lecturer = User::factory()->create(['role' => UserRole::Lecturer]);

        $this->actingAs($lecturer)
            ->get(route('dashboard.users.index'))
            ->assertForbidden();
    }

    public function test_legacy_dashboard_admin_urls_redirect_to_dashboard_paths(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/dashboard/admin/users')
            ->assertRedirect('/dashboard/users');
    }

    public function test_monitoring_url_redirects_to_system_settings(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/dashboard/monitoring')
            ->assertRedirect('/dashboard/system');
    }

    public function test_security_logs_route_redirects_to_activity_logs(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get(route('dashboard.security-logs.index'))
            ->assertRedirect('/dashboard/activity-logs');
    }

    public function test_admin_can_update_runtime_system_toggles(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->put(route('dashboard.system.update'), [
                'support_email' => 'support@example.edu',
                'monitoring_banner_enabled' => '0',
                'admin_dashboard_auto_refresh_enabled' => '1',
                'admin_dashboard_auto_refresh_seconds' => '45',
            ])
            ->assertRedirect(route('dashboard.system.edit'));

        $this->assertSame('support@example.edu', Cache::get('examiq.support_email'));
        $this->assertFalse((bool) Cache::get('examiq.monitoring_banner_enabled'));
        $this->assertTrue((bool) Cache::get('examiq.admin_dashboard_auto_refresh_enabled'));
        $this->assertSame(45, (int) Cache::get('examiq.admin_dashboard_auto_refresh_seconds'));
    }

    public function test_admin_cannot_create_duplicate_faculty_for_same_university(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $university = University::query()->create(['name' => 'Takoradi Technical University']);
        Faculty::query()->create([
            'university_id' => $university->getKey(),
            'name' => 'Faculty of Engineering',
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.faculties.store'), [
                'university_id' => $university->getKey(),
                'name' => '  Faculty   of Engineering  ',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_cannot_create_duplicate_university_name(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        University::query()->create(['name' => 'Takoradi Technical University']);

        $this->actingAs($admin)
            ->post(route('dashboard.universities.store'), [
                'name' => 'Takoradi Technical University',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_non_super_admin_cannot_reset_system_data(): void
    {
        Config::set('examiq.super_admin_phones', ['0200000000']);
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'phone' => '0201234567',
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.system.reset-data'), [
                'confirmation_text' => 'RESET ALL DATA',
                'reset_level' => 'default',
            ])
            ->assertForbidden();
    }

    public function test_super_admin_can_trigger_system_data_reset(): void
    {
        Config::set('examiq.super_admin_phones', ['0200000000']);
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'phone' => '0200000000',
        ]);

        $mock = \Mockery::mock(SystemDataResetService::class);
        $mock->shouldReceive('resetByLevel')->once()->withArgs(function ($user, $level) {
            return $user instanceof User && $level === 'default';
        });
        $this->app->instance(SystemDataResetService::class, $mock);

        $this->actingAs($admin)
            ->post(route('dashboard.system.reset-data'), [
                'confirmation_text' => 'RESET ALL DATA',
                'reset_level' => 'default',
            ])
            ->assertRedirect(route('dashboard.system.edit'));
    }

    public function test_hod_can_create_user_when_toggle_enabled(): void
    {
        Cache::forever('examiq.allow_hod_user_management', true);
        $university = University::query()->create(['name' => 'U-HOD-1']);
        $faculty = Faculty::query()->create(['university_id' => $university->id, 'name' => 'F-HOD-1']);
        $department = Department::query()->create(['faculty_id' => $faculty->id, 'name' => 'D-HOD-1']);
        $hod = User::factory()->create([
            'role' => UserRole::Hod,
            'department_id' => $department->id,
        ]);

        $this->actingAs($hod)
            ->post(route('dashboard.department.users.store'), [
                'name' => 'New Lecturer',
                'phone' => '0249990001',
                'role' => 'lecturer',
                'department_id' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'New Lecturer',
            'phone' => '0249990001',
            'role' => 'lecturer',
            'department_id' => $department->id,
        ]);
    }

    public function test_hod_cannot_create_user_when_toggle_disabled(): void
    {
        Cache::forever('examiq.allow_hod_user_management', false);
        $university = University::query()->create(['name' => 'U-HOD-2']);
        $faculty = Faculty::query()->create(['university_id' => $university->id, 'name' => 'F-HOD-2']);
        $department = Department::query()->create(['faculty_id' => $faculty->id, 'name' => 'D-HOD-2']);
        $hod = User::factory()->create([
            'role' => UserRole::Hod,
            'department_id' => $department->id,
        ]);

        $this->actingAs($hod)
            ->post(route('dashboard.department.users.store'), [
                'name' => 'Blocked Attempt',
                'phone' => '0249990002',
                'role' => 'moderator',
            ])
            ->assertForbidden();
    }
}
