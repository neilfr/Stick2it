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
            "description" => 'string',
            "quantity" => 'required|integer|min:0',
            "kcal" => 'required|integer|min:0',
            "fat" => 'required|integer|min:0',
            "protein" => 'required|integer|min:0',
            "carbohydrate" => 'required|integer|min:0',
            "potassium" => 'required|integer|min:0',
            "consumed_at" => 'required|date',
        ];
    }
}
