<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan jika perlu auth
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:50',
            'address'    => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Name wajib diisi.',
            'name.max'         => 'Name maksimal 255 karakter.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Email harus berupa alamat email yang valid.',
            'email.max'        => 'Email maksimal 255 karakter.',
            'phone.required'   => 'Phone wajib diisi.',
            'phone.max'        => 'Phone maksimal 50 karakter.',
            'address.required' => 'Address wajib diisi.',
            'address.max'      => 'Address maksimal 255 karakter.',
        ];
    }
}
