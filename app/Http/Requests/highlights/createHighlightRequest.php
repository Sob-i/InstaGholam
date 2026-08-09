<?php

namespace App\Http\Requests\highlights;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class createHighlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:2|max:255',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'stories' => 'required|array|min:1',
            'stories.*' => 'required|integer|exists:stories,id',
        ];
    }
}
