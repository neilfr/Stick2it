<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    public function foodgroup()
    {
        return $this->belongsTo(Foodgroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function foodsource()
    {
        return $this->belongsTo(Foodsource::class);
    }

    public function parentfoods()
    {
        return $this->belongsToMany(Food::class, 'ingredients', 'ingredient_id', 'parent_food_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredients', 'parent_food_id', 'ingredient_id')
            // ->as('ingredients')
            ->withPivot('id', 'quantity');
    }
}
