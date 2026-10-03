<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBos() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
        ];
    }
}
