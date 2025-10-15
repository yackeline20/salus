<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        // Generar secreto si no existe
        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        // Generar URL del QR
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->Nombre_Usuario,
            $user->google2fa_secret
        );

        // Generar imagen QR
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeImage = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.two-factor-setup', [
            'qrCodeImage' => $qrCodeImage,
            'secret' => $user->google2fa_secret
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric|digits:6'
        ]);

        $google2fa = new Google2FA();
        $user = auth()->user();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $user->google2fa_enabled = true;
            $user->save();

            return redirect()->route('dashboard')->with('success', '¡Autenticación de dos pasos habilitada correctamente!');
        }

        return back()->withErrors(['one_time_password' => 'El código ingresado es inválido']);
    }

    public function disable()
    {
        $user = auth()->user();
        $user->google2fa_enabled = false;
        $user->save();

        return back()->with('success', 'Autenticación de dos pasos deshabilitada');
    }
}