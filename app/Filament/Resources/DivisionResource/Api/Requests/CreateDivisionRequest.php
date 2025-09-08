<?php

namespace App\Filament\Resources\DivisionResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDivisionRequest extends FormRequest
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
			'division_name' => 'required',
			'required_workhours' => 'required|integer',
			'is_deleted' => 'required'
		];
    }
}
