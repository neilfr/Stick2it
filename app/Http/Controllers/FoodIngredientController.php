<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Inertia\Inertia;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Http\Resources\IngredientResource;
use App\Http\Requests\CreateIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;

class FoodIngredientController extends Controller
{
    public function index(Food $food)
    {
        if ($food->user_id === auth()->user()->id) {
            $ingredients = $food->ingredients;
            return Inertia::render('Foods/Ingredients/Index', [
                'ingredients' => IngredientResource::collection($ingredients),
            ]);
        }

        return redirect()->route('foods.index');
    }

    public function store(CreateIngredientRequest $request, Food $food)
    {
        if ($food->user_id === auth()->user()->id) {
            $payload = $request->input();
            $food = Food::find($food->id);
            $ingredient = Food::find($payload['ingredient_id']);
            if (! $food->ingredients->contains($ingredient->id)) {
                $food->ingredients()->attach(
                    $payload['ingredient_id'],
                    ['quantity' => isset($payload['quantity'])? $payload['quantity']: $ingredient->base_quantity]
                );
            }
            return redirect()->route('foods.show', $food);
        }
        return redirect()->route('foods.index');
    }

    public function update(UpdateIngredientRequest $request, Food $food, Ingredient $ingredient)
    {
        if ($food->user_id === auth()->user()->id) {
            $payload = $request->input();
            $food->ingredients()->updateExistingPivot($ingredient->id, ['quantity' => $payload['quantity']]);
            return redirect()->route('foods.show', $food);
        }
        return redirect()->route('foods.index');
    }

}
