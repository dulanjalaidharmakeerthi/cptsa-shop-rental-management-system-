<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /** Show registration form */
    public function show()
    {
        return view('auth.register');
    }

    /** Handle registration */
    public function register(Request $request)
    {
        $data = $request->only(['name','email','phone','position','password','password_confirmation']);

        $validator = Validator::make($data, [
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'phone' => ['required','string','max:30'],
            'position' => ['required','string','in:Manager,Officer,Staff,Vendor,Cashier,Other'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'position' => $data['position'],
            'password' => Hash::make($data['password']),
        ]);

        // optionally log the user in: Auth::login($user);

        return redirect('/')->with('success','Registration successful. Please login.');
    }
}
