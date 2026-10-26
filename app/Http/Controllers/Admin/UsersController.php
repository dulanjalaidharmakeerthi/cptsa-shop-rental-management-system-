<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    /**
     * Show the form for creating a new user (Admin version)
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage (Admin version)
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'phone', 'position', 'password', 'password_confirmation']);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['required', 'string', 'in:Manager,Staff,Cashier,Accountant'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'],
                'password' => Hash::make($data['password']),
                'is_blocked' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create admin user', [
                'error' => $e->getMessage(),
                'request' => $request->except(['password', 'password_confirmation']),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to create user. Please try again or contact support.');
        }

        return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
    }

    /**
     * Display all registered users for admin management.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display a single user's profile for admin view/control.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->withErrors(['User not found.']);
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle the blocked state for a user.
     */
    public function toggleBlock($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->withErrors(['User not found.']);
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'blocked' : 'unblocked';

        return redirect()->route('admin.users.index')->with('success', "User successfully {$status}.");
    }

    /**
     * Delete a registered user account.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->withErrors(['User not found.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted successfully.');
    }
}
