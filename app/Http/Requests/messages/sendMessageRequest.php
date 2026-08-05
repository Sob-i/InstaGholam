<?php

namespace App\Http\Requests\messages;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class sendMessageRequest extends FormRequest
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
            'chat_id' => 'required|integer|exists:chats,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string',
            'attachments' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
            'type' => 'required|in:message,reply',
        ];
    }
}
