<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ════════════════════════════════════════════
    // LOGIN
    // ════════════════════════════════════════════

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Block Google-only accounts from password login
        if ($user && $user->isGoogleUser() && is_null($user->password)) {
            return back()->withErrors([
                'email' => 'Akun ini terdaftar via Google. Silakan login dengan Google.',
            ])->onlyInput('email');
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // If email not verified yet, send to verification
        if (!Auth::user()->hasVerifiedEmail()) {
            $this->sendVerificationCode(Auth::user());
            return redirect()->route('verification.notice');
        }

        return $this->redirectBasedOnRole(Auth::user());
    }

    // ════════════════════════════════════════════
    // REGISTER
    // ════════════════════════════════════════════

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
            'has_seen_onboarding' => false,
        ]);

        Auth::login($user);
        $this->sendVerificationCode($user);

        return redirect()->route('verification.notice');
    }

    // ════════════════════════════════════════════
    // EMAIL VERIFICATION
    // ════════════════════════════════════════════

    public function showVerificationNotice()
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.verify-email');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi salah.']);
        }

        if ($user->verification_code_expires_at && now()->isAfter($user->verification_code_expires_at)) {
            return back()->withErrors(['code' => 'Kode verifikasi sudah kedaluwarsa. Minta kode baru.']);
        }

        $user->update([
            'email_verified_at'              => now(),
            'verification_code'              => null,
            'verification_code_expires_at'   => null,
        ]);

        return $this->redirectBasedOnRole($user);
    }

    public function resendCode(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Rate limit: 1 resend per 60 seconds
        if ($user->verification_code_expires_at && $user->verification_code_expires_at->diffInSeconds(now()) > 540) {
            return back()->withErrors(['code' => 'Tunggu sebentar sebelum meminta kode baru.']);
        }

        $this->sendVerificationCode($user);

        return back()->with('status', 'Kode verifikasi baru telah dikirim ke email kamu.');
    }

    private function sendVerificationCode(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code'            => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
    }

    // ════════════════════════════════════════════
    // FORGOT PASSWORD
    // ════════════════════════════════════════════

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Kami tidak menemukan akun dengan email tersebut.',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && $user->isGoogleUser() && is_null($user->password)) {
            return back()->withErrors([
                'email' => 'Akun ini terdaftar via Google dan tidak memiliki kata sandi.',
            ]);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', '✅ Link reset kata sandi telah dikirim ke email kamu.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', '✅ Kata sandi berhasil direset. Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }

    // ════════════════════════════════════════════
    // LOGOUT
    // ════════════════════════════════════════════

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════

    private function redirectBasedOnRole(User $user)
    {
        return $user->role === 'admin'
            ? redirect()->intended('/admin')
            : redirect()->intended('/dashboard');
    }
}
