<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your One-Time PIN</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #2c3e50; margin-bottom: 10px;">🔐 Verify Your Account</h2>
            <p style="font-size: 16px; color: #555;">Your One-Time PIN is shown below</p>
        </div>

        <div style="text-align: center; margin: 40px 0;">
            <span style="display: inline-block; font-size: 36px; font-weight: bold; letter-spacing: 6px; color: #2d3436;">{{ $otp }}</span>
        </div>

        <p style="font-size: 15px; line-height: 1.6; color: #444;">
            This One-Time PIN (Personal Identification Number) is valid for a short period of time. Please enter it promptly to complete your verification.
        </p>

        <p style="font-size: 14px; color: #888; margin-top: 20px;">
            Do not share this PIN with anyone. If you did not request this, you can safely ignore this message.
        </p>

        <hr style="margin: 40px 0; border: none; border-top: 1px solid #eee;">

        <div style="text-align: center; font-size: 13px; color: #aaa;">
            &copy; {{ date('Y') }} Enrollment Management System. All rights reserved.
        </div>
    </div>
</body>
</html>
