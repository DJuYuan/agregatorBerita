<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'rss_url'     => ['required', 'url', 'unique:sources,rss_url'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama sumber berita wajib diisi.',
            'name.max'             => 'Nama sumber tidak boleh melebihi 100 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'rss_url.required'     => 'URL RSS wajib diisi.',
            'rss_url.url'          => 'Format URL tidak valid. Pastikan menggunakan http:// atau https://.',
            'rss_url.unique'       => 'URL RSS ini sudah terdaftar di sistem.',
        ];
    }
}
