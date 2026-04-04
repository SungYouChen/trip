<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    // The password is now managed via config/app.php ('anniversary_password')

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials, true)) {
            $request->session()->regenerate();
            
            if ($request->ajax()) {
                return response()->json([
                    'message' => '登入成功！',
                    'redirect' => route('home', ['user' => auth()->user()])
                ]);
            }

            return redirect()->route('home', ['user' => auth()->user()])->with('success', '登入成功');
        }

        if ($request->ajax()) {
            return response()->json(['message' => '信箱或密碼錯誤。'], 422);
        }

        return back()->with('error', '信箱或密碼錯誤。');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', '已成功登出。');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $credentials['name'],
            'username' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        event(new Registered($user));

        auth()->login($user);

        if ($request->ajax()) {
            return response()->json([
                'message' => '註冊成功！請檢查您的信箱進行驗證。',
                'redirect' => route('verification.notice')
            ]);
        }

        return redirect()->route('verification.notice')->with('success', '註冊成功！請檢查您的信箱進行驗證。');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/')->with('success', '您的電子郵件已成功驗證！');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => '您的電子郵件已經驗證過了。']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => '驗證連結已重新發送至您的信箱！']);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Password::sendResetLink(
            $request->only('email')
        );

        if ($status === \Password::RESET_LINK_SENT) {
            return response()->json(['message' => '重設密碼連結已發送至您的信箱！請檢查信箱（包含垃圾郵件箱）。']);
        }

        return response()->json(['message' => '無法處理此信箱的重設請求，請檢查信箱是否正確。'], 422);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = \Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Str::random(60));

                $user->save();
            }
        );

        if ($status === \Password::PASSWORD_RESET) {
            return redirect('/')->with('success', '密碼已成功重設！請重新登入。');
        }

        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => __($status)]);
    }
}
