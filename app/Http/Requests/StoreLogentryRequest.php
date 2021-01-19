<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogentryRequest extends FormRequest
{

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
