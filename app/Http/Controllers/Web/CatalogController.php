<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\StudentContextService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    private const CATALOGS = [
        'resources' => [
            'label' => 'Resources',
            'eyebrow' => 'Academic library',
            'description' => 'Find focused learning material for your field and current work.',
        ],
        'tools' => [
            'label' => 'Tools',
            'eyebrow' => 'Study toolkit',
            'description' => 'Explore practical platforms for writing, research, coding, and planning.',
        ],
        'templates' => [
            'label' => 'Templates',
            'eyebrow' => 'Template library',
            'description' => 'Use structured academic formats for assignments, reports, and research.',
        ],
    ];

    public function __invoke(
        Request $request,
        string $catalog,
        StudentContextService $studentContext,
    ): View|RedirectResponse {
        $student = $studentContext->forUser($request->user());

        if (! $student) {
            return redirect()->route('onboarding');
        }

        abort_unless(isset(self::CATALOGS[$catalog]), 404);

        return view('pages.app.catalog', [
            'catalog' => ['key' => $catalog, ...self::CATALOGS[$catalog]],
            'student' => $student,
            'user' => $request->user(),
        ]);
    }
}
