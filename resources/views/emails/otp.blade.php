<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .header {
            background-color: #1976d2;
            color: white;
            padding: 20px;
            border-radius: 4px 4px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .otp-box {
            background-color: #fff;
            border: 2px solid #1976d2;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-box .otp-value {
            font-size: 36px;
            font-weight: bold;
            color: #1976d2;
            letter-spacing: 5px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hello {{ $userName }},</p>

            <p>You have requested to reset your password for the Shop Rental Management System. To proceed, please use the following One-Time Password (OTP):</p>

            <div class="otp-box">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Your OTP is:</p>
                <div class="otp-value">{{ $otp }}</div>
            </div>

            <p style="text-align: center; margin-top: 20px;">
                <strong>This OTP will expire in 10 minutes.</strong>
            </p>

            <div class="warning">
                ⚠️ Please do not share this OTP with anyone. We will never ask for your OTP via email or phone.
            </div>

            <p>If you did not request a password reset, please ignore this email or contact our support team immediately.</p>

            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

            <p style="font-size: 14px; color: #666;">
                Best regards,<br>
                <strong>{{ config('app.name') }} Support Team</strong>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
