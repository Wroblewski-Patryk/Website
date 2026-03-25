<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Rules\TranslationKeyConsistency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $query = Translation::query();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('key', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");

                try {
                    $activeCodes = \App\Models\Language::where('is_active', true)->pluck('code');
                    foreach ($activeCodes as $code) {
                        $sub->orWhere("text->{$code}", 'like', "%{$search}%");
                    }
                } catch (\Exception $e) {
                    // Fallback if DB doesn't support JSON search or languages table missing
                }
            });
        });

        if ($request->filled('sort') && $request->filled('direction')) {
            $sort = (string) $request->sort;
            $direction = strtolower((string) $request->direction) === 'asc' ? 'asc' : 'desc';

            $allowedSorts = ['group', 'key', 'created_at', 'updated_at'];
            $activeCodes = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
            foreach ($activeCodes as $code) {
                $allowedSorts[] = "text.{$code}";
            }

            if (in_array($sort, $allowedSorts, true)) {
                if (str_contains($sort, '.')) {
                    $sort = str_replace('.', '->', $sort);
                }
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('group')->orderBy('key');
            }
        } else {
            $query->orderBy('group')->orderBy('key');
        }

        $coverage = $this->buildCoverageSummary();

        return Inertia::render('Admin/Translations/Index', [
            'translations_list' => $query->paginate(20)->withQueryString(),
            'coverage' => $coverage,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_\-]+$/'],
            'key' => [
                'required',
                'string',
                'max:191',
                new TranslationKeyConsistency((string) $request->group),
                'unique:translations,key,NULL,id,group,' . $request->group,
            ],
            'text' => 'required|array',
        ]);


        Translation::create($validated);

        return redirect()->back()->with('success', 'translations.create_success');
    }

    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'text' => 'required|array',
        ]);

        $translation->update($validated);

        return redirect()->back()->with('success', 'translations.update_success');
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        return redirect()->back()->with('success', 'translations.delete_success');
    }

    /**
     * Build translation coverage dashboard payload from current scan-backed DB records.
     */
    protected function buildCoverageSummary(): array
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('code')
            ->get(['code', 'name', 'is_default']);

        $translations = Translation::query()->get(['group', 'text']);
        $totalKeys = $translations->count();

        $perGroup = $translations
            ->groupBy('group')
            ->map(function ($groupRows, $group) use ($totalKeys) {
                $count = $groupRows->count();
                return [
                    'group' => $group,
                    'keys' => $count,
                    'share_percent' => $totalKeys > 0
                        ? round(($count / $totalKeys) * 100, 1)
                        : 0.0,
                ];
            })
            ->sortByDesc('keys')
            ->values()
            ->all();

        $languageCoverage = $languages
            ->map(function (Language $language) use ($translations, $totalKeys) {
                $filled = $translations->filter(function (Translation $translation) use ($language) {
                    $value = $translation->getTranslation('text', $language->code, false);
                    return is_string($value) && trim($value) !== '';
                })->count();

                $missing = max(0, $totalKeys - $filled);

                return [
                    'code' => $language->code,
                    'name' => $language->name,
                    'is_default' => (bool) $language->is_default,
                    'filled' => $filled,
                    'missing' => $missing,
                    'coverage_percent' => $totalKeys > 0
                        ? round(($filled / $totalKeys) * 100, 1)
                        : 0.0,
                ];
            })
            ->values()
            ->all();

        $fullyCoveredLanguages = collect($languageCoverage)
            ->where('coverage_percent', 100.0)
            ->count();

        return [
            'total_keys' => $totalKeys,
            'languages' => $languageCoverage,
            'fully_covered_languages' => $fullyCoveredLanguages,
            'groups' => $perGroup,
        ];
    }
}
