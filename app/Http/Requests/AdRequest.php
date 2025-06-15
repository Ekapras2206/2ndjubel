<?php

namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\Auth;

    class AdRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            // Hanya admin yang boleh mengelola iklan
            return Auth::check() && Auth::user()->isAdmin();
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Untuk upload/update gambar
                'link' => 'nullable|url|max:255',
                'type' => 'required|in:banner,featured_product,homepage_promo',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'user_id' => 'nullable|exists:users,id', // Jika iklan terkait user tertentu
            ];
        }
    }