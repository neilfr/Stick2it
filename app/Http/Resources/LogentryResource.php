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
        return [
            'id' => $this->id,
            'user' => $this->user,
            'quantity' => $this->quantity,
            'food' => $this->food,
            'consumed_at' => Carbon::parse($this->consumed_at)->format('Y-m-d'),
        ];
    }
}
