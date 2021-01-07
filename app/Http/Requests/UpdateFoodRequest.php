<?php

namespace App\Http\Requests;

use App\Models\Food;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodRequest extends FormRequest
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
            'alias' => [
                'string',
                'nullable',
                Rule::unique('foods', 'alias')->where(function ($query) {
                    return $query->where([
                        ['user_id', '=', auth()->user()->id],
                        ['id', '<>', $this->food->id],
                    ]);
                }),
            ],
            'description' => [
                'string',
                Rule::unique('foods', 'description')->where(function ($query) {
                    return $query->where([
                        ['user_id', '=', auth()->user()->id],
                        ['id', '<>', $this->food->id],
                    ]);
                }),
            ],
            'kcal' => 'integer|min:0',
            'fat' => 'integer|min:0',
            'protein' => 'integer|min:0',
            'carbohydrate' => 'integer|min:0',
            'potassium' => 'integer|min:0',
            'base_quantity' => 'integer|min:0',
            'foodgroup_id' => 'exists:App\Models\Foodgroup,id',
            'foodsource_id' => 'exists:App\Models\Foodsource,id',
            'user_id' => 'exists:App\Models\User,id',
        ];
    }
}
