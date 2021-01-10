<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'alias' => $this->alias,
            'kcal' => $this->kcal,
            'fat' => $this->fat,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
            'potassium' => $this->potassium,
            'base_quantity' => $this->base_quantity,
            'foodgroup_id' => $this->foodgroup_id,
            'foodsource_id' => $this->foodsource_id,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'editable' => $this->user_id === auth()->user()->id,
            'ingredients' => IngredientResource::collection($this->ingredients),
            'favourite' => User::find(auth()
                ->user()->id)
                ->favourites()
                ->where('food_id', $this->id)
                ->exists(),
        ];
    }
}
