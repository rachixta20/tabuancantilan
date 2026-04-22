<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'     => ['required', 'string', 'max:20'],
            'role'      => ['required', 'in:farmer,buyer'],
            'location'  => ['required', 'string', 'max:255'],
            'password'  => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];

        if ($this->input('role') === 'farmer') {
            $rules['farm_name']     = ['required', 'string', 'max:255'];
            $rules['id_type']       = ['required', 'string', 'max:100'];
            $rules['id_document']   = ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
            $rules['selfie_photo']  = ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
            $rules['farm_document'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
            $rules['barangay']      = ['required', 'string', 'max:100'];
            $rules['purok']         = ['nullable', 'string', 'max:50'];
            $rules['street']        = ['nullable', 'string', 'max:255'];
            $rules['latitude']      = ['nullable', 'numeric', 'between:-90,90'];
            $rules['longitude']     = ['nullable', 'numeric', 'between:-180,180'];
        }

        return $rules;
    }
}
