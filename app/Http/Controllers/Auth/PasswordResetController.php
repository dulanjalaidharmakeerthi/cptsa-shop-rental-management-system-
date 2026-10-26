<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password - send OTP
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'No account found with this email address.');
        }

        // Delete expired OTP requests
        PasswordResetRequest::where('email', $email)
            ->where('verified', false)
            ->where('otp_expires_at', '<', Carbon::now())
            ->delete();

        // Check for existing valid OTP (not expired)
        $existingRequest = PasswordResetRequest::where('email', $email)
            ->where('verified', false)
            ->where('otp_expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if ($existingRequest && $existingRequest->isOtpValid()) {
            return redirect()->back()->with('error', 'An OTP has already been sent to this email. Please check your inbox.');
        }

        // Generate OTP (6 digits)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create password reset request
        $resetRequest = PasswordResetRequest::create([
            'email' => $email,
            'otp' => $otp,
            'attempts' => 0,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
            'verified' => false,
        ]);

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otp, $user->name));
            return redirect()->route('password.verify-otp-form')
                ->with('success', 'OTP has been sent to your email. Please check your inbox.')
                ->with('email', $email);
        } catch (\Exception $e) {
            // Delete the reset request if email fails
            $resetRequest->delete();
            
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('OTP Email Send Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to send OTP: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->query('email') ?? session('email');
        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $otp = $request->otp;

        // Find the latest password reset request
        $resetRequest = PasswordResetRequest::getLatestRequest($email);

        if (!$resetRequest) {
            return redirect()->back()
                ->with('error', 'No password reset request found. Please request a new OTP.')
                ->withInput();
        }

        // Check if OTP is expired
        if ($resetRequest->isOtpExpired()) {
            $resetRequest->delete();
            return redirect()->route('password.forgot')
                ->with('error', 'OTP has expired. Please request a new one.');
        }

        // Check attempts
        if ($resetRequest->attempts >= 5) {
            $resetRequest->delete();
            return redirect()->route('password.forgot')
                ->with('error', 'Too many failed attempts. Please request a new OTP.');
        }

        // Verify OTP
        if ($resetRequest->otp !== $otp) {
            $resetRequest->increment('attempts');
            $remaining = 5 - $resetRequest->attempts;
            return redirect()->back()
                ->with('error', "Invalid OTP. Please try again. ($remaining attempts remaining)")
                ->withInput();
        }

        // Mark as verified
        $resetRequest->update(['verified' => true]);

        // Store in session for next step
        session(['reset_email' => $email, 'reset_request_id' => $resetRequest->id]);

        return redirect()->route('password.reset-form')
            ->with('success', 'OTP verified successfully. Please enter your new password.')
            ->with('email', $email);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request)
    {
        // Check if user has verified OTP
        if (!session('reset_request_id')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Please verify your OTP first.');
        }

        $email = session('reset_email');
        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        // Verify session
        if (!session('reset_request_id')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expired. Please try again.');
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $password = $request->password;

        // Get the reset request
        $resetRequest = PasswordResetRequest::find(session('reset_request_id'));

        if (!$resetRequest || !$resetRequest->verified || $resetRequest->email !== $email) {
            session()->forget(['reset_email', 'reset_request_id']);
            return redirect()->route('password.forgot')
                ->with('error', 'Invalid reset request.');
        }

        // Check if reset request is still valid
        if ($resetRequest->isOtpExpired()) {
            $resetRequest->delete();
            session()->forget(['reset_email', 'reset_request_id']);
            return redirect()->route('password.forgot')
                ->with('error', 'Reset request has expired.');
        }

        // Find and update user password
        $user = User::where('email', $email)->first();
        if (!$user) {
            session()->forget(['reset_email', 'reset_request_id']);
            return redirect()->route('password.forgot')
                ->with('error', 'User not found.');
        }

        // Update password
        $user->update(['password' => Hash::make($password)]);

        // Delete reset request
        $resetRequest->delete();

        // Clear session
        session()->forget(['reset_email', 'reset_request_id']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }
}
