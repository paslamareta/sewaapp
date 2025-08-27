<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan jika perlu auth
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'version'     => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|url|max:255',
            'status'      => 'required|in:active,inactive',
            'type'        => 'required|in:web,mobile,desktop',
            'host_id'     => 'nullable|exists:hosts,id', // pastikan ada relasi dengan model Host
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Name wajib diisi.',
            'name.max'         => 'Name maksimal 255 karakter.',
            'version.max'      => 'Version maksimal 50 karakter.',
            'version.required'  => 'Version wajib diisi.',
            'description.max'  => 'Description maksimal 1000 karakter.',
            'link.url'         => 'Link harus berupa URL yang valid.',
            'status.required'  => 'Status wajib diisi.',
            'status.in'        => 'Status harus active atau inactive.',
            'type.required'    => 'Type wajib diisi.',
            'type.in'          => 'Type harus web, mobile, atau desktop.',
            'host_id.exists'   => 'Host tidak ditemukan.',
            'host_id.nullable' => 'Host ID boleh kosong jika tidak ada host yang terkait.',
        ];
    }
}
