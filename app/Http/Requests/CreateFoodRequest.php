<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFoodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'alias' => [
              'string',
              'nullable',
                Rule::unique('foods', 'alias')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
            ],
            'description' => [
                'required',
                'string',
                Rule::unique('foods', 'description')->where(function ($query){
                    return $query->where('user_id', auth()->user()->id);
                }),
            ],
            'kcal' => 'required|integer|min:0',
            'fat' => 'required|integer|min:0',
            'protein' => 'required|integer|min:0',
            'carbohydrate' => 'required|integer|min:0',
            'potassium' => 'required|integer|min:0',
            'base_quantity' => 'required|integer|min:0',
            // 'foodgroup_id' => 'required|exists:App\Models\Foodgroup,id',
            // 'foodsource_id' => 'required|exists:App\Models\Foodsource,id',
            'user_id' => 'required|exists:App\Models\User,id',
        ];
    }
}
