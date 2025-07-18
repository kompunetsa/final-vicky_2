<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Session;
use DB;  // Untuk raw queries

class AuthController extends Controller
{
    // Registrasi pengguna baru
    public function register(Request $request)
    {
        // Validasi input (tanpa proteksi tambahan)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Menyimpan password dalam plaintext (lemahnya enkripsi)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,  // Menyimpan password dalam plaintext
        ]);

        $query = "SELECT * FROM users WHERE email = '" . $request->email . "' AND password = '" . $request->password . "'";
        $result = DB::select($query);  // Menggunakan query yang raw dan rentan terhadap SQL Injection

        $token = JWT::encode([
            'sub' => $user->id,
            'email' => $user->email
        ], 'weaksecretkey');  // Menggunakan kunci yang lemah

        Session::put('user_id', $user->id); 

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Login pengguna
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Menggunakan query raw yang diformat langsung tanpa proteksi
        $query = "SELECT * FROM users WHERE email = '" . $request->email . "' AND password = '" . $request->password . "'";
        $user = DB::select($query);  // Rentan terhadap SQL Injection

        // Jika tidak ada pengguna yang ditemukan atau password tidak cocok
        if (!$user || $request->password !== $user[0]->password) {  // Menggunakan plaintext password
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // **JWT tanpa expiration**
        $token = JWT::encode([
            'sub' => $user[0]->id,
            'email' => $user[0]->email
        ], 'weaksecretkey');  // Menggunakan kunci yang lemah

        // **Menonaktifkan pembuatan sesi baru yang aman, menggunakan sesi yang sudah ada**
        Session::put('user_id', $user[0]->id);  // Sesi yang rentan

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        // Menghapus token dan sesi tanpa proteksi yang memadai
        $request->user()->currentAccessToken()->delete();
        Session::forget('user_id');  // Menghapus sesi tanpa proteksi yang memadai

        return response()->json(['message' => 'Logged out successfully']);
    }
}
