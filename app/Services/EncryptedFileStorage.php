<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploaded citizen documents (ID scans, supporting documents)
 * encrypted at rest using AES-256 (Laravel's default Crypt cipher),
 * instead of writing the raw file bytes straight to disk. The original
 * extension and MIME type are preserved in the returned manifest so the
 * file can be decrypted and re-served with the correct Content-Type.
 */
class EncryptedFileStorage
{
    /**
     * Encrypt and store an uploaded file.
     *
     * @return string The storage path of the encrypted blob.
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'private'): string
    {
        $ciphertext = Crypt::encryptString(base64_encode(file_get_contents($file->getRealPath())));

        $filename = Str::uuid()->toString() . '.enc';
        $path = trim($directory, '/') . '/' . $filename;

        Storage::disk($disk)->put($path, $ciphertext);

        return $path;
    }

    /**
     * Decrypt a previously stored file and return its raw binary contents.
     */
    public static function retrieve(string $path, string $disk = 'private'): string
    {
        $ciphertext = Storage::disk($disk)->get($path);

        return base64_decode(Crypt::decryptString($ciphertext));
    }
}
