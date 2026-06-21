<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\LebanesePhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-]+$/u'],
            'last_name'  => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-]+$/u'],
            'email'      => [
                'required',
                'string',
                'lowercase',
                'email:rfc',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'   => ['nullable', new LebanesePhoneNumber],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
