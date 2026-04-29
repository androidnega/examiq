<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversityController extends Controller
{
    public function index(): View
    {
        $universities = University::query()
            ->withCount('faculties')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.universities.index', [
            'universities' => $universities,
        ]);
    }

    public function create(): View
    {
        return view('admin.universities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => $this->normalizedName((string) $request->input('name', '')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities', 'name')],
        ]);

        University::query()->create($data);

        return redirect()
            ->route('dashboard.universities.index')
            ->with('status', __('University created.'));
    }

    public function edit(University $university): View
    {
        return view('admin.universities.edit', [
            'university' => $university,
        ]);
    }

    public function update(Request $request, University $university): RedirectResponse
    {
        $request->merge([
            'name' => $this->normalizedName((string) $request->input('name', '')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities', 'name')->ignore($university->getKey())],
        ]);

        $university->update($data);

        return redirect()
            ->route('dashboard.universities.index')
            ->with('status', __('University updated.'));
    }

    public function destroy(University $university): RedirectResponse
    {
        if ($university->faculties()->exists()) {
            return back()->withErrors(['delete' => __('Remove faculties under this university first.')]);
        }

        $university->delete();

        return redirect()
            ->route('dashboard.universities.index')
            ->with('status', __('University deleted.'));
    }

    protected function normalizedName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    }
}
