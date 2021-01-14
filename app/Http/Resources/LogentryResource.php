<?php

namespace App\Http\Resources;

use App\Models\Food;
use Illuminate\Http\Resources\Json\JsonResource;

class LogentryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $food = Food::find($this->food_id);
        return [
            'id' => $this->id,
            'consumed_at' => $this->consumed_at,
            'food_id' => $this->food_id,
            'food_description' => $food->description,
            'food_alias' => $food->alias,
            'quantity' => $this->quantity,
        ];
    }
}
