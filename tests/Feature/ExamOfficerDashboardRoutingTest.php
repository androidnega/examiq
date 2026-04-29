<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamOfficerDashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_officer_home_is_dashboard_not_registry(): void
    {
        $officer = User::factory()->create(['role' => UserRole::ExamOfficer]);

        $home = $this->actingAs($officer)->get('/dashboard');
        $home->assertOk();
        $home->assertDontSee(__('Approved examinations only — search and sort the registry.'), false);

        $registry = $this->actingAs($officer)->get('/dashboard/registry');
        $registry->assertOk();
        $registry->assertSee(__('Total records'), false);
    }

    public function test_login_redirect_path_points_to_dashboard_for_exam_officer(): void
    {
        $officer = User::factory()->create(['role' => UserRole::ExamOfficer]);

        $path = parse_url(route('dashboard'), PHP_URL_PATH);
        $this->assertSame('/dashboard', $path);

        $this->actingAs($officer)->get('/login')->assertRedirect('/dashboard');
    }
}
