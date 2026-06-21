<?php

namespace App\Http\Requests\Auth;

use App\Rules\LebanesePhoneNumber;
use App\Rules\ValidFileSignature;
use Illuminate\Foundation\Http\FormRequest;

class CitizenRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-]+$/u'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-]+$/u'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', new LebanesePhoneNumber],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120', new ValidFileSignature],
            'id_document_type' => ['required', 'in:passport,national_id,drivers_license'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'The first name may only contain letters, spaces, and hyphens.',
            'last_name.regex' => 'The last name may only contain letters, spaces, and hyphens.',
            'email.email' => 'Please enter a valid, deliverable email address.',
            'id_document.mimes' => 'The ID document must be a PDF, JPG, or PNG file.',
        ];
    }
}