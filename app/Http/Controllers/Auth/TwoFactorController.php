<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', [
            'email' => $user->email,
        ]);
    }

    public function store(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login')->withErrors(['code' => __('Your session expired. Please sign in again.')]);
        }

        if (! $twoFactor->verify($user, $request->input('code'))) {
            return back()->withErrors(['code' => __('The code is invalid or expired.')]);
        }

        $twoFactor->clear($user);

        Auth::login($user);
        $request->session()->forget(TwoFactorService::SESSION_KEY);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    protected function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get(TwoFactorService::SESSION_KEY);

        if (! $id) {
            return null;
        }

        return User::find($id);
    }
}
