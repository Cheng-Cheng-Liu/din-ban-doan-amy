<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RestaurantRequest extends FormRequest
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
            'name' => 'required|string|max:256',
            'tag' => 'nullable|string|max:256',
            'phone' => 'nullable|string|max:60',
            'opening_time' => 'nullable|string|max:256',
            'closing_time' => 'nullable|string|max:256',
            'rest_day' => 'nullable|string|max:7',
            'status' => 'required|int|max:100',
            'priority' => 'nullable|int|max:1000',
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            response()->json(['error' => 1001])
        ]));
    }
}
