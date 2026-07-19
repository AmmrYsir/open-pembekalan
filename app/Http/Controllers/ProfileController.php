<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sequence;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        return view('profile', compact('user'));
    }
}
