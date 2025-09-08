<?php

namespace App\Filament\Resources\MeetingResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMeetingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'meeting_title' => 'required',
			'meeting_note' => 'required|string',
			'date' => 'required|date',
			'start_time' => 'required',
			'end_time' => 'required',
			'is_deleted' => 'required'
		];
    }
}
