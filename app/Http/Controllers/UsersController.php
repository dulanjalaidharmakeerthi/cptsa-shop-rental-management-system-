<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UsersController extends Controller
{
    /** Show create user form (manager only) */
    public function create()
    {
        $user = Auth::user();
        if (! $user || strtolower($user->position ?? '') !== 'manager') {
            abort(403, 'Forbidden');
        }
        return view('auth.create_user');
    }

    /** Store a new user (manager only) */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user || strtolower($user->position ?? '') !== 'manager') {
            abort(403, 'Forbidden');
        }

        $data = $request->only(['name','email','phone','position','password','password_confirmation']);

        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'phone' => ['nullable','string','max:30'],
            'position' => ['required','string','in:Manager,Staff,Cashier'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.create')->with('success', 'User created successfully.');
    }
}
