<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if ($admin) {
            $passwordMatches = false;

            try {
                $passwordMatches = Hash::check($request->password, $admin->password);
            } catch (RuntimeException $e) {
                if ($admin->password === $request->password) {
                    $admin->password = Hash::make($request->password);
                    $admin->save();
                    $passwordMatches = true;
                }
            }

            if ($passwordMatches) {
                session(['admin_id' => $admin->id]);
                return redirect()->intended('/admin');
            }
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_id');
        return redirect('/admin/login');
    }

    public function profile()
    {
        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Please login to access profile.']);
        }

        return view('admin.profile', [
            'admin' => $admin,
            'readonly' => true,
        ]);
    }

    public function editProfile()
    {
        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Please login to edit your profile.']);
        }

        return view('admin.profile', [
            'admin' => $admin,
            'readonly' => false,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Please login to update your profile.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,' . $admin->id,
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin->name = $data['name'];
        $admin->username = $data['username'];
        $admin->email = $data['email'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function dashboard()
    {
        $admin = null;
        if (session('admin_id')) {
            $admin = Admin::find(session('admin_id'));
        }
        // counts for dashboard cards
        $totalShops = \App\Models\Shop::count();
        // count shops with any missing agreement date (start or end)
        $pendingAgreements = \App\Models\Shop::whereNull('agreement_start')
            ->orWhereNull('agreement_end')
            ->count();
        $activeUsers = \App\Models\User::count();

        // calculate total revenue from payments (amount + interest)
        $totalRevenue = \App\Models\Payment::selectRaw('SUM(amount + interest) as total')->value('total') ?? 0;

        // revenue for the current calendar year
        $currentYear = now()->year;
        $totalRevenueYear = \App\Models\Payment::whereYear('paid_at', $currentYear)
            ->selectRaw('SUM(amount + interest) as total')->value('total') ?? 0;

        return view('admin.dashboard', [
            'admin' => $admin,
            'totalShops' => $totalShops,
            'pendingAgreements' => $pendingAgreements,
            'activeUsers' => $activeUsers,
            'totalRevenue' => $totalRevenue,
            'totalRevenueYear' => $totalRevenueYear,
            'paymentEditModeEnabled' => $this->getPaymentEditModeSetting(),
        ]);

    }

    protected function getPaymentEditModeSetting()
    {
        if (!Storage::disk('local')->exists('payment-settings.json')) {
            return false;
        }

        $settings = json_decode(Storage::disk('local')->get('payment-settings.json'), true);
        return isset($settings['payment_edit_mode_enabled']) ? (bool) $settings['payment_edit_mode_enabled'] : false;
    }

    public function updatePaymentEditModeSetting(Request $request)
    {
        $request->validate([
            'payment_edit_mode_enabled' => 'required|boolean',
        ]);

        $settings = [
            'payment_edit_mode_enabled' => (bool) $request->input('payment_edit_mode_enabled'),
        ];

        Storage::disk('local')->put('payment-settings.json', json_encode($settings, JSON_PRETTY_PRINT));

        return redirect('/admin#permissions')->with('success', 'Payment edit mode setting updated successfully.');
    }
}
