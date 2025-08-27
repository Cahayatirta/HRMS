<?php

namespace App\Filament\Resources\ServiceResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
			'client_id' => 'required|integer',
			'service_type_id' => 'required|integer',
			'status' => 'required',
			'price' => 'required|integer',
			'start_time' => 'required',
			'expired_time' => 'required',
			'is_deleted' => 'required'
		];
    }
}
