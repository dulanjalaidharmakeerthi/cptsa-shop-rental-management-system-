<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    /** Show create admin form and list of existing admins */
    public function create()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.admins.create', compact('admins'));
    }

    /** Show all registered admins */
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /** Store new admin */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'nullable|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Admin created successfully.');
    }
}
