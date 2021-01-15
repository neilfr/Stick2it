<?php

namespace App\Http\Resources;

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
            'food' => new FoodResource($this->food),
            'quantity' => $this->quantity,
            'consumed_at' => $this->consumed_at,
        ];
    }
}
