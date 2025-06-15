<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\Auth;

    class ProfileUpdateRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return Auth::check();
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'], // 'confirmed' akan mencari password_confirmation
            ];
        }
    }
