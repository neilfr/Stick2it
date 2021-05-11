<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Inertia\Inertia;
use App\Models\Logentry;
use App\Models\Foodgroup;
use Illuminate\Http\Request;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\LogentryResource;
use App\Http\Resources\FoodgroupResource;
use App\Http\Requests\StoreLogentryRequest;
use App\Http\Requests\UpdateLogentryRequest;

class LogentryController extends Controller
{
    public function index(Request $request)
    {
        $paginatedLogentries = Logentry::query()
                ->userLogEntries()
                ->inDateRange($request->query('from'), $request->query('to'))
                ->paginate(Config::get('stick2it.paginator.per_page'));
        $allLogentries = Logentry::query()
                ->userLogEntries()
                ->inDateRange($request->query('from'), $request->query('to'))
                ->with('food')
                ->get();

        $nutrientValues= [
            'kcal' => 0,
            'fat' => 0,
            'protein' => 0,
            'carbohydrate' => 0,
            'potassium' => 0
        ];

        $allLogentries->each(function($logentry) use(&$nutrientValues) {
            $nutrientValues['kcal'] += $logentry->food->kcal;
            $nutrientValues['fat'] += $logentry->food->fat;
            $nutrientValues['protein'] += $logentry->food->protein;
            $nutrientValues['carbohydrate'] += $logentry->food->carbohydrate;
            $nutrientValues['potassium'] += $logentry->food->potassium;
        });

        return Inertia::render('Logentries/Index',[
            'page' => $paginatedLogentries->currentPage(),
            'logentries' => LogentryResource::collection($paginatedLogentries),
            'totalKcal' => $nutrientValues['kcal'],
            'totalFat' => $nutrientValues['fat'],
            'totalProtein' => $nutrientValues['protein'],
            'totalCarbohydrate' => $nutrientValues['carbohydrate'],
            'totalPotassium' => $nutrientValues['potassium'],
            'averageKcal' => round($nutrientValues['kcal']/$allLogentries->count()),
            'averageFat' => round($nutrientValues['fat']/$allLogentries->count()),
            'averageProtein' => round($nutrientValues['protein']/$allLogentries->count()),
            'averageCarbohydrate' => round($nutrientValues['carbohydrate']/$allLogentries->count()),
            'averagePotassium' => round($nutrientValues['potassium']/$allLogentries->count()),
        ]);
    }

    public function create(Request $request)
    {
        $foodgroups = Foodgroup::all();
        $foods = Food::userFoods()
            ->sharedFoods()
            ->foodgroupSearch($request->query('foodgroupSearch'))
            ->descriptionSearch($request->query('descriptionSearch'))
            ->aliasSearch($request->query('aliasSearch'))
            ->favouritesFilter($request->query('favouritesFilter') ? $request->query('favouritesFilter') : 'yes')
            ->with('ingredients')
            ->paginate(Config::get('stick2it.paginator.per_page'));

        return Inertia::render('Logentries/Create',[
            'foods' => FoodResource::collection($foods),
            'foodgroups' => FoodgroupResource::collection($foodgroups),
        ]);
    }

    public function store(StoreLogentryRequest $request)
    {
        Logentry::create($request->validated());

        return redirect()->route('logentries.index');
    }

    public function edit(Request $request)
    {
        $logentry = Logentry::find($request->logentry);
        $foodgroups = Foodgroup::all();
        $foods = Food::userFoods()
            ->sharedFoods()
            ->foodgroupSearch($request->query('foodgroupSearch'))
            ->descriptionSearch($request->query('descriptionSearch'))
            ->aliasSearch($request->query('aliasSearch'))
            ->favouritesFilter($request->query('favouritesFilter') ? $request->query('favouritesFilter') : 'yes')
            ->with('ingredients')
            ->paginate(Config::get('stick2it.paginator.per_page'));

        return Inertia::render('Logentries/Edit',[
            'logentry' => new LogentryResource($logentry),
            'foods' => FoodResource::collection($foods),
            'foodgroups' => FoodgroupResource::collection($foodgroups),
        ]);
    }

    public function update(UpdateLogentryRequest $request, Logentry $logentry)
    {
        if($logentry->user_id === auth()->user()->id) {
            $logentry->update($request->validated());
        }
        return redirect()->route('logentries.index');
    }

    public function destroy(Logentry $logentry)
    {
        $logentry->delete();
        return redirect()->route('logentries.index');
    }
}
