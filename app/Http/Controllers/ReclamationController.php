<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sujet' => 'required',
            'description' => 'required',
        ]);

        $userId = Auth::id();

        $reclamation = Reclamation::create([
            'user_id' => $userId,
            'sujet' => $request->sujet,
            'description' => $request->description,
        ]);

        return response()->json($reclamation, 201);
    }
}
