<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CitizenRegistrationRequest;
use App\Models\User;
use App\Services\EncryptedFileStorage;
use Illuminate\Support\Facades\Auth;

class CitizenRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.citizen-register');
    }

    public function register(CitizenRegistrationRequest $request)
    {
        // 1. Handle File Upload (Secure Vault) — the ID scan is encrypted
        // with AES-256 before it ever touches disk, so a storage-level
        // breach alone cannot expose citizens' identity documents.
        $path = EncryptedFileStorage::store($request->file('id_document'), 'id_vault', 'private');

        // 2. Create User Record
        $user = User::create([
            'first_name'             => $request->first_name,
            'last_name'              => $request->last_name,
            'email'                  => $request->email,
            'phone'                  => $request->phone,
            'password'               => $request->password,
            'id_document_path'       => $path,
            'id_document_type'       => $request->id_document_type,
            'id_verification_status' => 'pending',
            'role'                   => 'citizen',
            'status'                 => 'active',
        ]);

        // 3. Authenticate
        Auth::login($user);

        // 4. Redirect
        return redirect('/dashboard')->with('success', 'Registration successful! Your ID is under review.');
    }
}