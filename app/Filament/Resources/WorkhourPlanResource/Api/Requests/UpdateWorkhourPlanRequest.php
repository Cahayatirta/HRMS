<?php

namespace App\Filament\Resources\WorkhourPlanResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkhourPlanRequest extends FormRequest
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
			'plan_date' => 'required|date',
			'planned_starttime' => 'required',
			'planned_endtime' => 'required',
			'work_location' => 'required',
			'is_deleted' => 'required'
		];
    }
}
