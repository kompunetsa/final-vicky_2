<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Session;
use DB;

class AuthController extends Controller
{
    /**
     * Registrasi pengguna baru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validasi input dengan benar
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hash password sebelum disimpan
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Hash password dengan aman
        ]);

        // Mengambil user menggunakan Eloquent, bukan query raw
        $user = User::where('email', $request->email)->first();

        // Membuat payload JWT dengan expiration dan secret key yang kuat
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600,  // Token berlaku selama 1 jam
        ];
        $token = JWT::encode($payload, env('JWT_SECRET', 'your-strong-secret-key'));

        // Menyimpan user_id ke session
        Session::put('user_id', $user->id);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Login pengguna
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email menggunakan Eloquent
        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan password cocok dengan hash yang tersimpan
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        // Buat JWT dengan expiration dan secret key yang kuat
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600,  // Token berlaku selama 1 jam
        ];
        $token = JWT::encode($payload, env('JWT_SECRET', 'your-strong-secret-key'));

        // Simpan user_id ke session
        Session::put('user_id', $user->id);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout pengguna
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Hapus token akses saat ini jika tersedia
        if ($request->user() && method_exists($request->user(), 'currentAccessToken')) {
            $request->user()->currentAccessToken()->delete();
        }
        // Hapus session user_id
        Session::forget('user_id');

        return response()->json(['message' => 'Logout berhasil']);
    }
}
