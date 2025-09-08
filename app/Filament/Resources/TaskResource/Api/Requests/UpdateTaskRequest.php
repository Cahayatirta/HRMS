<?php

namespace App\Filament\Resources\TaskResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
			'task_name' => 'required',
			'task_description' => 'required|string',
			'deadline' => 'required|date',
			'status' => 'required',
			'parent_task_id' => 'required|integer',
			'note' => 'required|string',
			'is_deleted' => 'required'
		];
    }
}
