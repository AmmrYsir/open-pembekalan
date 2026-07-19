<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        return view('profile', compact('user'));
    }
}
