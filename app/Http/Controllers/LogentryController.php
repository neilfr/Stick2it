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
use Carbon\Carbon;

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

        $totalNutrientValues= [
            'kcal' => 0,
            'fat' => 0,
            'protein' => 0,
            'carbohydrate' => 0,
            'potassium' => 0
        ];

        $allLogentries->each(function($logentry) use(&$totalNutrientValues) {
            $totalNutrientValues['kcal'] += $logentry->food->kcal;
            $totalNutrientValues['fat'] += $logentry->food->fat;
            $totalNutrientValues['protein'] += $logentry->food->protein;
            $totalNutrientValues['carbohydrate'] += $logentry->food->carbohydrate;
            $totalNutrientValues['potassium'] += $logentry->food->potassium;
        });

        $logentryCount = $allLogentries->count();

        $todaysNutrientValues= [
            'kcal' => 0,
            'fat' => 0,
            'protein' => 0,
            'carbohydrate' => 0,
            'potassium' => 0
        ];

        $todaysLogEntries = $allLogentries->filter(function($logentry) {
            return Carbon::createFromFormat('Y-m-d H:i:s',$logentry->consumed_at)->format('Y-m-d') === Carbon::today()->format('Y-m-d');
        });

        $todaysLogEntries->each(function($logentry) use(&$todaysNutrientValues) {
            $todaysNutrientValues['kcal'] += $logentry->food->kcal;
            $todaysNutrientValues['fat'] += $logentry->food->fat;
            $todaysNutrientValues['protein'] += $logentry->food->protein;
            $todaysNutrientValues['carbohydrate'] += $logentry->food->carbohydrate;
            $todaysNutrientValues['potassium'] += $logentry->food->potassium;
        });

        return Inertia::render('Logentries/Index',[
            'page' => $paginatedLogentries->currentPage(),
            'logentries' => LogentryResource::collection($paginatedLogentries),
            'totalKcal' => $totalNutrientValues['kcal'],
            'totalFat' => $totalNutrientValues['fat'],
            'totalProtein' => $totalNutrientValues['protein'],
            'totalCarbohydrate' => $totalNutrientValues['carbohydrate'],
            'totalPotassium' => $totalNutrientValues['potassium'],
            'todaysKcal' => $todaysNutrientValues['kcal'],
            'todaysFat' => $todaysNutrientValues['fat'],
            'todaysProtein' => $todaysNutrientValues['protein'],
            'todaysCarbohydrate' => $todaysNutrientValues['carbohydrate'],
            'todaysPotassium' => $todaysNutrientValues['potassium'],
            'averageKcal' => $logentryCount > 0 ? round($totalNutrientValues['kcal']/$logentryCount): 0,
            'averageFat' => $logentryCount ? round($totalNutrientValues['fat']/$logentryCount): 0,
            'averageProtein' => $logentryCount ? round($totalNutrientValues['protein']/$logentryCount): 0,
            'averageCarbohydrate' => $logentryCount ? round($totalNutrientValues['carbohydrate']/$logentryCount): 0,
            'averagePotassium' => $logentryCount ? round($totalNutrientValues['potassium']/$logentryCount): 0,
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
