<?php

namespace App\Filament\Resources\EmployeeResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
			'user_id' => 'required|integer',
			'division_id' => 'required|integer',
			'full_name' => 'required',
			'gender' => 'required',
			'birth_date' => 'required|date',
			'phone_number' => 'required',
			'address' => 'required|string',
			'image_path' => 'nullable|string',
			'status' => 'required',
			'is_deleted' => 'required'
		];
    }
}
