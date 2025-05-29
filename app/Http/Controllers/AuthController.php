<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersLoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $key = Str::lower($request->username) . $request->ip();

        $reachLimit = RateLimiter::attempt($key, 5, function () {
            // jika limit terpenuhi maka return nya adalah ini
            return true;
        }, 60);

        if (!$reachLimit) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'username' => trans('auth.throttle', [
                    'seconds' => $seconds,
                ])
            ]);
        }

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $agent = new Agent();

        // Ambil informasi user
        $user = User::where('username', $request->username)->first();

        // Coba login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Log::info('Authentication successful : ', ['user_id' => $user->id]); // Log ID user yang berhasil login

            // Menyimpan log login sukses
            try {
                UsersLoginLog::create([
                    'user_id' => $user->id, // Ambil user_id dari user yang login
                    'username' => $user->username, // Ambil username dari user yang login
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'browser' => $agent->browser(),
                    'platform' => $agent->platform(),
                    'device' => $agent->device(),
                    'status' => 'success',
                    'failed_reason' => null,
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating login log', ['error' => $e->getMessage()]); // Debug log
            }

            return redirect()->intended('dashboard')->with('success', 'Login berhasil')->with('noback', true);
        }

        // Jika login gagal
        if ($user) {
            // Jika ada, berarti password salah
            $failedReason = 'Password salah';
        } else {
            // Jika user tidak ditemukan
            $failedReason = 'Username tidak ditemukan';
        }

        // Menyimpan log login gagal
        UsersLoginLog::create([
            'user_id' => $user ? $user->id : null, // Jika user ada, ambil user_id, jika tidak null
            'username' => $request->username, // Username tetap dicatat
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'status' => 'failed',
            'failed_reason' => $failedReason,
            'created_at' => now(),
        ]);

        return back()->withErrors([
            'username' => trans('auth.login.failed'),
        ])->withInput($request->only('username', 'password'));
    }

    public function register()
    {
        return view('auth.register');
    }

    // Proses Registrasi
    public function doRegister(Request $request)
    {
        // 'email' => 'required|string|email|max:255|unique:users',
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);


        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password'])
        ]);

        if (!$user) {
            return back()->withErrors([
                'username' => 'Error hubungi admin.',
            ])->withInput($request->only(['username', 'name']));
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout');
    }

    // Halaman Ubah Password
    public function changePassword()
    {
        return view('auth.change-password');
    }

    // Proses Ubah Password
    public function doChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama tidak sesuai.']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }

    public function profile(Request $request)
    {
        return view('auth.profile', [
            'user' => $request->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email,' . $user->id
            'username' => 'required|username|unique:users,username,' . $user->id
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username
        ]);

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
