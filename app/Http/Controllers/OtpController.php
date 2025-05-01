<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\OtpLog;
use App\Mail\OtpMail; // Make sure to import your Mailable class too

class OtpController extends Controller
{
    public function showForm()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required']);


        $otp = implode('', $request->otp);
        $request->merge(['otp' => $otp]);
        // dd($request->otp);
        $user = auth()->user();
        if ($user->otp === $request->otp) {
            $user->is_verified = true;
            $user->otp = null;
            $user->save();

            return redirect('/home')->with('status', 'OTP Verified!');
        }

        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }

    public function resend()
    {
        // $user = auth()->user();
        // $otp = rand(100000, 999999);
        // $user->otp = $otp;
        // $user->save();

        // Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        // return back()->with('status', 'OTP resent to your email!');

        $user = auth()->user();
        $cacheKey = 'otp_resend_' . $user->id;

        // Get the user agent and IP address
        $userAgent = request()->header('User-Agent');
        $ipAddress = request()->ip();

        // Check if the user recently requested
        if (Cache::has($cacheKey)) {
            $secondsLeft = Cache::get($cacheKey) - time();
            return response()->json([
                'status' => 'error',
                'message' => "Please wait {$secondsLeft} seconds before requesting again.",
                'seconds_left' => $secondsLeft
            ]);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->save();

        // Send the OTP via email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Log the OTP sent attempt
        OtpLog::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'email' => $user->email,
            'status' => 'sent',  // Status is 'sent' since it's the first OTP generation
            'user_agent' => $userAgent,  // Log the user agent
            'ip_address' => $ipAddress,  // Log the IP address
        ]);

        // Log the resend attempt
        OtpLog::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'email' => $user->email,
            'status' => 'resend',  // Status is 'resend' because the user requested a new OTP
            'user_agent' => $userAgent,  // Log the user agent
            'ip_address' => $ipAddress,  // Log the IP address
        ]);

        // Set a cooldown for 30 seconds
        Cache::put($cacheKey, time() + 30, 30);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP resent successfully!'
        ]);
    }
}
