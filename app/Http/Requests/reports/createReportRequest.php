<?php

namespace App\Http\Requests\reports;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class createReportRequest extends FormRequest
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
            'reporter_id' => 'required|integer',
            'reported_user_id' => 'required|integer',
            'reported_post_id' => 'nullable|integer',
            'reported_comment_id' => 'nullable|integer',
            'reported_story_id' => 'nullable|integer',
            'report_subject'=> 'required|in:spam,harassment,hate_speech,violence,nudity,false_information,other',
        ];
    }
}
