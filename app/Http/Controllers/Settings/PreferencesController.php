<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class PreferencesController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $prefs = $user->preferences;

        $sources = \App\Models\Article::query()
            ->distinct()
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->orderBy('source')
            ->pluck('source')
            ->values()
            ->toArray();

        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $authors = Author::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Settings/Preferences', [
            'preferences' => [
                'preferred_sources' => $prefs?->preferred_sources ?? [],
                'preferred_category_ids' => $prefs?->preferred_category_ids ?? [],
                'preferred_author_ids' => $prefs?->preferred_author_ids ?? [],
            ],
            'sources' => $sources,
            'categories' => $categories,
            'authors' => $authors,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_sources' => ['nullable', 'array'],
            'preferred_sources.*' => ['string', 'max:255'],
            'preferred_category_ids' => ['nullable', 'array'],
            'preferred_category_ids.*' => ['integer', 'exists:categories,id'],
            'preferred_author_ids' => ['nullable', 'array'],
            'preferred_author_ids.*' => ['integer', 'exists:authors,id'],
        ]);

        $user = $request->user();
        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_sources' => $validated['preferred_sources'] ?? [],
                'preferred_category_ids' => $validated['preferred_category_ids'] ?? [],
                'preferred_author_ids' => $validated['preferred_author_ids'] ?? [],
            ]
        );

        return back()->with('success', 'Preferences saved.');
    }
}
