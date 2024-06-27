<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderRequest extends FormRequest
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
            'user_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'restaurant_id' => 'required|integer|min:1|max:9223372036854775807',
            'amount' => 'required|integer|min:-2147483648|max:100000',
            'status' => 'required|integer|min:-128|max:127',
            'remark' => 'required|string|max:255',
            'pick_up_time' => 'required|string|max:255',
            'created_time' => 'required|string|max:255',
        ];

        // Detail validation rules
        if ($this->detail && is_array($this->detail)) {
            foreach ($this->detail as $index => $detail) {
                $rules["detail.$index.meal_name"] = 'required|string|max:255';
                $rules["detail.$index.price"] = 'required|integer|min:1|max:100000';
                $rules["detail.$index.quantity"] = 'required|integer|max:11';
                $rules["detail.$index.amount"] = 'required|integer|min:1|max:100000';
            }
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => __('error.invalidParameters')
        ]));
    }
}
