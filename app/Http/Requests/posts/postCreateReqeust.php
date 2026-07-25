<?php

namespace App\Http\Requests\posts;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class postCreateReqeust extends FormRequest
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
            'caption'=>'nullable|min:3|max:2000',
            'tags'=>'nullable|min:2|max:255',
            'location'=>'nullable|min:3|max:255',
            'disable_comments'=>'required|in:open,closed',
            'hide_likes'=>'required|in:visible,notVisible',
            'uploadFile'=>'required|max:100000',
            'audience'=>'required|in:everyone,followers_only,close_friends',
        ];
    }
}
