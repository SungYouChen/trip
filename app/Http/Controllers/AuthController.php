<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // PASSWORD CONFIGURATION
    // Change this date to your anniversary date (YYYYMMDD)
    private const PASSWORD = '20190913';

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

        auth()->login($user);

        if ($request->ajax()) {
            return response()->json([
                'message' => '註冊成功！',
                'redirect' => route('home', ['user' => $user])
            ]);
        }

        return redirect()->route('home', ['user' => $user])->with('success', '註冊成功！');
    }
}
