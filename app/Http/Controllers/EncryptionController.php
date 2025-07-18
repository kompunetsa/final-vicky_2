<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
class EncryptionController extends Controller
{
    public function encryptData(Request $request)
    {
         $data = 'Sensitive Data';
        // Kunci acak 32-byte (untuk AES-256-CBC) — simpan di tempat aman
        $key = hash('sha256', 'kataacakadut', true); // hasil 32-byte
        // IV acak 16-byte
        $iv = random_bytes(16);
        // Enkripsi dengan AES-256-CBC (output RAW binary)
        $cipherText = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // Gabungkan IV + ciphertext, encode ke base64 agar mudah dikirim
        $encrypted = base64_encode($iv . $cipherText);
        return response()->json([
            'encrypted_data' => $encrypted
        ]);

    }

}

