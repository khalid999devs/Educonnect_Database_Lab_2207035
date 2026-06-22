<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('pages.app.workspace', [
            'user' => $request->user(),
        ]);
    }
}
