<?php

namespace App\Http\Requests\profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class profileEditInfoReqeust extends FormRequest
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'username' => 'required|min:3|max:20',
            'website' => 'nullable|max:80',
            'bio' => 'nullable|max:150',
        ];
    }
}
