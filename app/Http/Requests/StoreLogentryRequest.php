<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogentryRequest extends FormRequest
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
        return [
            "user_id" => 'required|integer|exists:users,id',
            "food_id" => 'required|integer|exists:foods,id',
            "quantity" => 'required|integer|min:0',
            "consumed_at" => 'required|date',
        ];
    }
}
