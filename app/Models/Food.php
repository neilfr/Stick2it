<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'alias',
        'description',
        'kcal',
        'fat',
        'protein',
        'carbohydrate',
        'potassium',
        'favourite',
        'base_quantity',
        'editable',
        'foodgroup_id',
        'foodsource_id',
        'user_id',
    ];


    public function scopeUserFoods(Builder $query)
    {
        $query->where('user_id', auth()->user()->id);
    }

    public function scopeSharedFoods(Builder $query)
    {
        $sharableFoodsourceIds = Foodsource::sharable()->get()->pluck('id');

        if ($sharableFoodsourceIds->isNotEmpty()) {
            $query->orWhere('foodsource_id', '=', $sharableFoodsourceIds);
        }
    }

    public function scopeAliasSearch(Builder $query, ?string $aliasSearch = null)
    {
        if (is_null($aliasSearch)) {
            return $query;
        }

        $query->where('alias', 'like', "%{$aliasSearch}%");
    }

    public function scopeDescriptionSearch(Builder $query, ?string $descriptionSearch=null)
    {
        if (is_null($descriptionSearch)) {
            return $query;
        }

        $query->where('description', 'like', "%{$descriptionSearch}%");
    }

    public function scopeFoodgroupSearch(Builder $query, $foodgroupSearch)
    {
        if (is_null($foodgroupSearch)) {
            return $query;
        }

        $query->where('foodgroup_id', '=', "{$foodgroupSearch}");
    }

    public function scopeFavouritesFilter(Builder $query, $favouritesFilter)
    {
        if (is_null($favouritesFilter) || $favouritesFilter==="no") {
            return $query;
        }
        if ($favouritesFilter==="yes") {
            $favouriteIds = User::find(auth()
                ->user()->id)
                ->favourites()->pluck('food_id');
            $query->whereIn('id', $favouriteIds);
        }
    }

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
