<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /** Handle login post */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // find user by email or name
        $user = User::where('email', $username)->orWhere('name', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return redirect()->back()->withErrors(['login' => 'Invalid username or password'])->withInput();
        }

        Auth::login($user);

        // redirect based on position
        $position = strtolower($user->position ?? '');
        if ($position === 'accountant') {
            return redirect('/managedashboard');
        }

        switch ($position) {
            case 'manager':
                return redirect()->intended('/managedashboard');
            case 'staff':
                return redirect()->intended('/staffdashboard');
            case 'cashier':
                return redirect()->intended('/cashierdashboard');
            default:
                return redirect()->intended('/dashboard');
        }
    }

    /** Logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
