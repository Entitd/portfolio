<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->normalizeSpaces($this->input('name')),
            'email' => $this->normalizeEmail($this->input('email')),
            'phone' => $this->normalizeSpaces($this->input('phone')),
            'comment' => $this->normalizeComment($this->input('comment')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'phone' => [
                'required',
                'string',
                'min:7',
                'max:32',
                'regex:/^\+?[0-9\s().-]+$/',
            ],
            'comment' => ['required', 'string', 'min:5', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'name.max' => 'Имя не должно превышать 100 символов.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Укажите корректный email.',
            'email.max' => 'Email не должен превышать 254 символа.',
            'phone.required' => 'Укажите телефон.',
            'phone.min' => 'Телефон должен содержать не менее 7 символов.',
            'phone.max' => 'Телефон не должен превышать 32 символа.',
            'phone.regex' => 'Телефон содержит недопустимые символы.',
            'comment.required' => 'Напишите комментарий.',
            'comment.min' => 'Комментарий должен содержать не менее 5 символов.',
            'comment.max' => 'Комментарий не должен превышать 3000 символов.',
        ];
    }

    private function normalizeSpaces(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return preg_replace('/\s+/u', ' ', trim($value));
    }

    private function normalizeEmail(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return Str::lower(trim($value));
    }

    private function normalizeComment(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }
}
