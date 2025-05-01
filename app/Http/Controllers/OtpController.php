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
        // Validate the OTP input (ensure all fields are filled)
        $request->validate(['otp' => 'required|array|min:6|max:6']); // Ensure the OTP has 6 digits

        // Combine the OTP input (from 6 separate fields) into a single string
        $otp = implode('', $request->otp);

        // Merge the OTP value into the request for further use
        $request->merge(['otp' => $otp]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the OTP entered by the user matches the OTP stored in the database
        if ($user->otp === $request->otp) {
            // OTP is correct, verify the user
            $user->is_verified = true;
            $user->otp = null; // Clear the OTP after verification
            $user->save();

            // Log the verification attempt as 'verified'
            OtpLog::create([
                'user_id' => $user->id,
                'otp' => $request->otp,
                'email' => $user->email,
                'status' => 'verified', // Log as 'verified' since the OTP is correct
                'user_agent' => request()->header('User-Agent'), // Log the user agent
                'ip_address' => request()->ip(), // Log the IP address
            ]);

            // Redirect the user with a success message
            return redirect('/home')->with('status', 'OTP Verified!');
        } else {
            // OTP is incorrect, log as 'failed'
            OtpLog::create([
                'user_id' => $user->id,
                'otp' => $request->otp,
                'email' => $user->email,
                'status' => 'failed', // Log as 'failed' because the OTP was incorrect
                'user_agent' => request()->header('User-Agent'), // Log the user agent
                'ip_address' => request()->ip(), // Log the IP address
            ]);

            // Return back with an error message if the OTP is invalid
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }
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

        // Check if the user already has an OTP that has been sent (you can adjust this condition if needed)
        $existingOtpLog = OtpLog::where('user_id', $user->id)->where('status', 'sent')->first();

        if ($existingOtpLog) {
            // Log the resend attempt if an OTP has already been sent to this user
            OtpLog::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'email' => $user->email,
                'status' => 'resend',  // Status is 'resend' because the user requested a new OTP
                'user_agent' => $userAgent,  // Log the user agent
                'ip_address' => $ipAddress,  // Log the IP address
            ]);
        } else {
            // Log the OTP sent attempt if this is the first OTP generation
            OtpLog::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'email' => $user->email,
                'status' => 'sent',  // Status is 'sent' since it's the first OTP generation
                'user_agent' => $userAgent,  // Log the user agent
                'ip_address' => $ipAddress,  // Log the IP address
            ]);
        }

        // Set a cooldown for 30 seconds
        Cache::put($cacheKey, time() + 30, 30);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP resent successfully!'
        ]);
    }
}
