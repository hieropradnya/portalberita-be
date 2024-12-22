<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        // validasi inputan user
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // cek apakah akun dengan email tersbut terdaftar
        $user = User::where('email', $request->email)->first();

        // jika datanya ($user) tidak ada di DB atau password salah 
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // buat token dan kirim ke klien
        return $user->createToken('user_login')->plainTextToken;
    }

    function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
    }

    function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
