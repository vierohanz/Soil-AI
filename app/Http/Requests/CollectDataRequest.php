<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CollectDataRequest extends FormRequest
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
            'temperature' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'air_humidity' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soil_humidity' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }

    public function messages(): array
    {
        return [
            'temperature.regex' => 'Temperature should be a number with up to 2 decimal places.',
            'air_humidity.regex' => 'Air Humidity should be a number with up to 2 decimal places.',
            'soil_humidity.regex' => 'Soil Humidity should be a number with up to 2 decimal places.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors(),
        ], 400));
    }
}
