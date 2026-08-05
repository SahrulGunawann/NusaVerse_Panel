<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $this->ensureSchemaAndAdminUser();

        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $rememberedLogin = Cookie::get('last_remembered_login', '');

        return view('auth.login', compact('rememberedLogin'));
    }

    public function login(Request $request)
    {
        $this->ensureSchemaAndAdminUser();

        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        // Rate Limiting Protection (Max 5 attempts per minute)
        $throttleKey = Str::lower($loginInput) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => ["Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."],
            ]);
        }

        $remember = $request->boolean('remember') || $request->has('remember');
        $loginSuccess = false;

        // 1. Try username if column exists
        if (Schema::hasColumn('users', 'username')) {
            if (Auth::attempt(['username' => $loginInput, 'password' => $password], $remember)) {
                $loginSuccess = true;
            }
        }

        // 2. Try email
        if (!$loginSuccess && Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
            $loginSuccess = true;
        }

        // 3. Try name
        if (!$loginSuccess && Auth::attempt(['name' => $loginInput, 'password' => $password], $remember)) {
            $loginSuccess = true;
        }

        if ($loginSuccess) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Save or clear remembered username/email cookie (30 days lifetime)
            if ($remember) {
                Cookie::queue('last_remembered_login', $loginInput, 43200);
            } else {
                Cookie::queue(Cookie::forget('last_remembered_login'));
            }

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali di NusaVerse Controller!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'Kombinasi Username/Email atau Kata Sandi salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari NusaVerse Controller.');
    }

    /**
     * Auto-ensure username column exists and admin user is seeded.
     */
    private function ensureSchemaAndAdminUser(): void
    {
        try {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'username')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('username')->nullable()->unique();
                });
            }

            if (Schema::hasTable('users')) {
                User::updateOrCreate(
                    ['email' => 'admin@nusaverse.com'],
                    [
                        'name' => 'NusaVerse Controller Admin',
                        'username' => 'nusaverse',
                        'email' => 'admin@nusaverse.com',
                        'password' => Hash::make('zevendevontop'),
                    ]
                );
            }
        } catch (\Exception $e) {
            // Silently swallow schema/seed errors if already configured
        }
    }
}
