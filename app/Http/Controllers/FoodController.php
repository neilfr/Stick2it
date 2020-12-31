<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Inertia\Inertia;
use App\Models\Foodgroup;
use Illuminate\Http\Request;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\FoodgroupResource;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $foods = Food::query()
        ->userFoods()
        ->sharedFoods()
        ->foodgroupSearch($request->query('foodgroupSearch'))
        ->descriptionSearch($request->query('descriptionSearch'))
        ->aliasSearch($request->query('aliasSearch'))
        ->favouritesFilter($request->query('favouritesFilter'))
        ->paginate(Config::get('stick2it.paginator.per_page'));

        $foodgroups = Foodgroup::all();

        return Inertia::render('Foods/Index', [
            'page' => $foods->currentPage(),
            'foods' => FoodResource::collection($foods),
            'foodgroups' => FoodgroupResource::collection($foodgroups),
        ]);
    }

    public function show(Request $request, Food $food)
    {
        $foodgroups = Foodgroup::all();
        $foods = Food::userFoods()
        ->sharedFoods()
        ->foodgroupSearch($request->query('foodgroupSearch'))
        ->descriptionSearch($request->query('descriptionSearch'))
        ->aliasSearch($request->query('aliasSearch'))
        ->favouritesFilter($request->query('favouritesFilter'))
        ->with('ingredients')
        ->paginate(Config::get('ml2.paginator.per_page'));

        if (($food->user_id === auth()->user()->id) || ((bool)$food->foodsource->sharable === true)){
            return Inertia::render('Foods/Show', [
                'food' => new FoodResource($food),
                'foods' => FoodResource::collection($foods),
                'foodgroups' => FoodgroupResource::collection($foodgroups),
            ]);
        }
        return redirect()->route('foods.index');
    }
}
