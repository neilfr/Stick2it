<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Food;
use App\Http\Resources\FoodResource;
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
        $scaleFactor = $this->quantity/$this->food->base_quantity;
        return [
            'id' => $this->id,
            'user' => $this->user,
            'quantity' => $this->quantity,
            'food' => $this->food,
            'kcal' => $this->food->kcal * $scaleFactor,
            'fat' => $this->food->fat * $scaleFactor,
            'protein' => $this->food->protein * $scaleFactor,
            'carbohydrate' => $this->food->carbohydrate * $scaleFactor,
            'potassium' => $this->food->potassium * $scaleFactor,
            'consumed_at' => Carbon::parse($this->consumed_at)->format('Y-m-d'),
        ];
    }
}
