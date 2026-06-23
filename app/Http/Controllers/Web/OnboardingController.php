<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\StudentContextService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __invoke(Request $request, StudentContextService $studentContext): View|RedirectResponse
    {
        if ($studentContext->forUser($request->user())) {
            return redirect()->route('workspace');
        }

        return view('pages.app.onboarding', [
            'user' => $request->user(),
        ]);
    }
}
