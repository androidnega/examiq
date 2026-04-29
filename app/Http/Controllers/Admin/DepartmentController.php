<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::query()
            ->with('faculty.university')
            ->withCount(['users', 'courses'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.departments.index', [
            'departments' => $departments,
        ]);
    }

    public function create(): View
    {
        return view('admin.departments.create', [
            'faculties' => Faculty::query()->with('university')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'faculty_id' => ['required', 'uuid', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Department::query()->create($data);

        return redirect()
            ->route('dashboard.departments.index')
            ->with('status', __('Department created.'));
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', [
            'department' => $department,
            'faculties' => Faculty::query()->with('university')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $data = $request->validate([
            'faculty_id' => ['required', 'uuid', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $department->update($data);

        return redirect()
            ->route('dashboard.departments.index')
            ->with('status', __('Department updated.'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->users()->exists() || $department->courses()->exists()) {
            return back()->withErrors(['delete' => __('Reassign users and courses before deleting this department.')]);
        }

        $department->delete();

        return redirect()
            ->route('dashboard.departments.index')
            ->with('status', __('Department deleted.'));
    }
}
