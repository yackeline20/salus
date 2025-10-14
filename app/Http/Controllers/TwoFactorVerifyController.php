<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;

class TwoFactorVerifyController extends Controller
{
    public function show()
    {
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric|digits:6'
        ]);

        $userId = session('2fa:user:id');
        $user = User::where('Cod_Usuario', $userId)->firstOrFail();

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            session()->forget('2fa:user:id');
            Auth::login($user);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['one_time_password' => 'El código ingresado es inválido']);
    }
}