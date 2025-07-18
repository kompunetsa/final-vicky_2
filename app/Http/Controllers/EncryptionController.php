<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncryptionController extends Controller
{
    public function encryptData(Request $request)
    {
        // Kunci lemah untuk AES-256-CBC
        $weakKey = '1234567890123456';  // Kunci lemah
        $iv = '1234567890123456';  // IV yang lemah (16 bytes)

        // Data yang akan dienkripsi
        $data = 'Sensitive Data';

        // Enkripsi menggunakan AES-256-CBC dengan kunci lemah
        $cipher = openssl_encrypt($data, 'AES-256-CBC', $weakKey, 0, $iv);

        return response()->json([
            'encrypted_data' => $cipher
        ]);
    }
}
