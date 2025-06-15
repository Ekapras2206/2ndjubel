<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Validation\Rule;

    class CategoryRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return Auth::check() && Auth::user()->isAdmin(); // Hanya admin
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            $categoryId = $this->route('category'); // Ambil ID kategori jika sedang update

            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($categoryId), // Unik kecuali dirinya sendiri saat update
                ],
                'description' => 'nullable|string',
            ];
        }
    }