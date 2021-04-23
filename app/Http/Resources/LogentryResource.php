<?php

namespace App\Http\Resources;

use App\Http\Resources\FoodResource;
use Carbon\Carbon;
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
        return [
            'id' => $this->id,
            'user' => $this->user,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'kcal' => $this->kcal,
            'fat' => $this->fat,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
            'potassium' => $this->potassium,

            'consumed_at' => Carbon::parse($this->consumed_at)->format('Y-m-d'),
        ];
    }
}
