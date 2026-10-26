<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'attempts',
        'otp_expires_at',
        'verified',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Check if OTP has expired
     */
    public function isOtpExpired()
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if OTP is still valid
     */
    public function isOtpValid()
    {
        return !$this->isOtpExpired() && $this->attempts < 5;
    }

    /**
     * Get the latest request for an email
     */
    public static function getLatestRequest($email)
    {
        return self::where('email', $email)->latest()->first();
    }
}
