<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo(Auth::user()));
        }

        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if ($request->role === 'farmer') {
            $data['account_status'] = 'pending';
            $data['is_verified']    = false;

            if ($request->hasFile('id_document')) {
                $data['id_document'] = $request->file('id_document')->store('verifications/ids', 'public');
            }
            if ($request->hasFile('selfie_photo')) {
                $data['selfie_photo'] = $request->file('selfie_photo')->store('verifications/selfies', 'public');
            }
            if ($request->hasFile('farm_document')) {
                $data['farm_document'] = $request->file('farm_document')->store('verifications/farms', 'public');
            }
        } else {
            $data['account_status'] = 'approved';
        }

        $user = User::create($data);
        Auth::login($user);

        if ($user->isFarmer()) {
            return redirect()->route('farmer.dashboard')
                ->with('info', 'Registration submitted! Your account is under review. We will notify you once approved.');
        }

        return redirect($this->redirectTo($user))->with('success', 'Welcome to TABUAN, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Password Reset

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully!')
            : back()->withErrors(['email' => __($status)]);
    }

    private function redirectTo(User $user): string
    {
        return match($user->role) {
            'admin'    => '/admin/dashboard',
            'farmer'   => '/farmer/dashboard',
            'verifier' => '/verifier/dashboard',
            default    => '/marketplace',
        };
    }
}
