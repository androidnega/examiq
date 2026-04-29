<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(): View
    {
        $faculties = Faculty::query()
            ->with('university')
            ->withCount('departments')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.faculties.index', [
            'faculties' => $faculties,
        ]);
    }

    public function create(): View
    {
        return view('admin.faculties.create', [
            'universities' => University::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => $this->normalizedName((string) $request->input('name', '')),
        ]);

        $data = $request->validate([
            'university_id' => ['required', 'uuid', 'exists:universities,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faculties')
                    ->where(fn ($query) => $query->where('university_id', $request->input('university_id'))),
            ],
        ]);
        Faculty::query()->create($data);

        return redirect()
            ->route('dashboard.faculties.index')
            ->with('status', __('Faculty created.'));
    }

    public function edit(Faculty $faculty): View
    {
        return view('admin.faculties.edit', [
            'faculty' => $faculty,
            'universities' => University::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $request->merge([
            'name' => $this->normalizedName((string) $request->input('name', '')),
        ]);

        $data = $request->validate([
            'university_id' => ['required', 'uuid', 'exists:universities,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faculties')
                    ->where(fn ($query) => $query->where('university_id', $request->input('university_id')))
                    ->ignore($faculty->getKey()),
            ],
        ]);
        $faculty->update($data);

        return redirect()
            ->route('dashboard.faculties.index')
            ->with('status', __('Faculty updated.'));
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        if ($faculty->departments()->exists()) {
            return back()->withErrors(['delete' => __('Remove departments under this faculty first.')]);
        }

        $faculty->delete();

        return redirect()
            ->route('dashboard.faculties.index')
            ->with('status', __('Faculty deleted.'));
    }

    protected function normalizedName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    }
}
