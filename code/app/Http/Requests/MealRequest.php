<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'restaurant_id' => 'required|int|between:-9223372036854775808,9223372036854775807',
            'name' => 'required|string|max:255',
            'price' => 'required|int',
            'another_id' => 'required|string|max:255',
            'status' => 'required|int'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => __('error.invalidParameters')

        ]));
    }
}
