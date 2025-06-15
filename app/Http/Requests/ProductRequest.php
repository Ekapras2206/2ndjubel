<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya user yang terautentikasi yang boleh mengunggah produk
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $imageRules = 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
                // Jika mode update (PUT/PATCH), gambar bisa jadi opsional
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $imageRules = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:99999999.99', // Max 10 digit total, 2 desimal
            'condition' => 'required|in:baru,bekas_baik,bekas_rusak',
            'category_id' => 'required|exists:categories,id', // Pastikan ID kategori ada di tabel categories
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'image' => $imageRules,
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Produk harus memiliki setidaknya satu gambar.',
            'images.max' => 'Produk tidak boleh memiliki lebih dari 5 gambar.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar yang diizinkan adalah JPEG, PNG, JPG, GIF, SVG.',
            'images.*.max' => 'Ukuran gambar maksimal adalah 2MB.',
            'price.max' => 'Harga maksimal adalah 99.999.999,99.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
