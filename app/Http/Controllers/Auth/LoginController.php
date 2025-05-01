<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // $otp = rand(100000, 999999); // Generate OTP
        // $user->otp = $otp;
        // $user->is_verified = false;
        // $user->save();

        // // Send OTP - use email, SMS, etc. For demo:
        // \Log::info("OTP for {$user->email}: {$otp}");

        // return redirect('/verify-otp');

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->is_verified = false;
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp));

        return redirect('/verify-otp');
    }
}
