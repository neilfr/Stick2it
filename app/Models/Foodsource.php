<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Foodsource extends Model
{
    use HasFactory;

    public function scopeSharable(Builder $query)
    {
        return $query->where('sharable', '=', true);
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
