<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminMenuPagesTest extends TestCase
{
    public function test_admin_role_middleware_forbids_non_admin_users(): void
    {
        $request = Request::create('/admin/students', 'GET');
        $request->setUserResolver(fn () => (object) ['role' => 'mahasiswa']);

        $response = (new EnsureAdminRole)->handle($request, fn () => new Response);

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_admin_role_middleware_allows_admin_users(): void
    {
        $request = Request::create('/admin/students', 'GET');
        $request->setUserResolver(fn () => (object) ['role' => 'admin']);
        $allowedResponse = new Response('Allowed.');

        $response = (new EnsureAdminRole)->handle($request, fn () => $allowedResponse);

        $this->assertSame($allowedResponse, $response);
    }

    public function test_admin_routes_apply_role_middleware(): void
    {
        $routes = app('router')->getRoutes();
        $studentsRoute = $routes->match(Request::create('/admin/students', 'GET'));
        $dashboardRoute = $routes->match(Request::create('/admin/dashboard', 'GET'));

        $this->assertContains('admin', $studentsRoute->gatherMiddleware());
        $this->assertContains('admin', $dashboardRoute->gatherMiddleware());
    }

    public function test_master_edit_route_binds_resource_and_id_in_controller_order(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/admin/lecturers/2/edit', 'GET'));

        $this->assertSame([
            'resource' => 'lecturers',
            'id' => '2',
        ], $route->parameters());
    }

    public function test_authenticated_non_admin_is_forbidden_from_admin_pages(): void
    {
        $user = new User;
        $user->setRawAttributes(['id' => 42, 'role' => 'mahasiswa']);

        $this->actingAs($user)
            ->get('/admin/students')
            ->assertForbidden();
    }

    public function test_students_page_handles_missing_class_data(): void
    {
        $html = view('admin.master.students', [
            'students' => [[
                'id' => 1,
                'nim' => 'S001',
                'name' => 'Test Student',
                'email' => 'student@example.com',
                'program' => 'Teknik Informatika',
                'status' => 'Aktif',
            ]],
        ])->render();

        $this->assertStringContainsString('Belum tersedia', $html);
    }

    public function test_lecturers_page_handles_missing_specialization_data(): void
    {
        $html = view('admin.master.lecturers', [
            'lecturers' => [[
                'id' => 2,
                'nidn' => 'D001',
                'name' => 'Test Lecturer',
                'email' => 'lecturer@example.com',
                'department' => 'Teknologi Informasi',
                'status' => 'Aktif',
            ]],
        ])->render();

        $this->assertStringContainsString('Belum tersedia', $html);
    }

    public function test_reservations_conflict_page_handles_group_without_requests(): void
    {
        $html = view('admin.reservations.index', [
            'pendingCount' => 0,
            'conflictCount' => 1,
            'approvedCount' => 0,
            'rejectedCount' => 0,
            'cancelledCount' => 0,
            'completedCount' => 0,
            'todayCount' => 0,
            'upcomingCount' => 0,
            'activeTab' => 'conflicts',
            'conflicts' => [[
                'room_name' => 'Ruang Uji',
                'date' => '2026-09-28',
                'requests' => [],
            ]],
            'reservations' => [],
        ])->render();

        $this->assertStringContainsString('Ruang Uji', $html);
    }
}
