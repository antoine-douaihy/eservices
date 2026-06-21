<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Verifies that an uploaded file's actual binary content (magic bytes)
 * matches one of the allowed types, rather than trusting the file
 * extension or the client-supplied Content-Type header. This stops a
 * disguised executable (e.g. malware.php renamed to malware.pdf.jpg)
 * from being accepted just because its extension looks safe.
 */
class ValidFileSignature implements ValidationRule
{
    /** Magic-byte signatures for the file types this app accepts. */
    private const SIGNATURES = [
        'pdf' => ["\x25\x50\x44\x46"],                  // %PDF
        'jpg' => ["\xFF\xD8\xFF"],                       // JPEG SOI marker
        'png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],   // PNG signature
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!($value instanceof UploadedFile) || !$value->isValid()) {
            $fail('The :attribute must be a valid uploaded file.');
            return;
        }

        $handle = @fopen($value->getRealPath(), 'rb');
        if (!$handle) {
            $fail('The :attribute could not be read for verification.');
            return;
        }

        $header = fread($handle, 16);
        fclose($handle);

        foreach (self::SIGNATURES as $type => $signatures) {
            foreach ($signatures as $signature) {
                if (str_starts_with($header, $signature)) {
                    return; // Matched a known-good signature — file is genuine.
                }
            }
        }

        $fail('The :attribute does not appear to be a genuine PDF, JPG, or PNG file. Please re-export or re-scan the document and try again.');
    }
}
