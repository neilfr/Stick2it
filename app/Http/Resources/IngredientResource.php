<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $scaleFactor = $this->pivot->quantity/$this->base_quantity;

        return [
            'id' => $this->id,
            'description' => $this->description,
            'alias' => $this->alias,
            'kcal' => $this->kcal * $scaleFactor,
            'fat' => $this-> fat * $scaleFactor,
            'protein' => $this-> protein * $scaleFactor,
            'carbohydrate' => $this-> carbohydrate * $scaleFactor,
            'potassium' => $this-> potassium * $scaleFactor,
            'base_quantity' => $this->base_quantity,
            'quantity' => $this->pivot->quantity,
            'foodgroup_id' => $this->foodgroup_id,
            'foodsource_id' => $this->foodsource_id,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'editable' => $this->user_id === auth()->user()->id,
        ];
    }
}
