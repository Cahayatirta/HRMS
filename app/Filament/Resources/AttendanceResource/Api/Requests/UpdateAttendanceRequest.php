<?php

namespace App\Filament\Resources\AttendanceResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
			'employee_id' => 'required|integer',
			'start_time' => 'required',
			'end_time' => 'required',
			'work_location' => 'required',
			'longitude' => 'required|numeric',
			'latitude' => 'required|numeric',
			'image_path' => 'required',
			'task_link' => 'required',
			'is_deleted' => 'required'
		];
    }
}
