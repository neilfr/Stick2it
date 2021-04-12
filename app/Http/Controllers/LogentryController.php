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
        $logentries = LogentryResource::collection(
            Logentry::query()
                ->userLogEntries()
                ->inDateRange($request->query('from'), $request->query('to'))
                ->paginate(Config::get('stick2it.paginator.per_page')));

        return Inertia::render('Logentries/Index',[
            'page' => $logentries->currentPage(),
            'logentries' => LogentryResource::collection($logentries),
            'totalKcal' => $logentries->sum('kcal'),
            'totalFat' => $logentries->sum('fat'),
            'totalProtein' => $logentries->sum('protein'),
            'totalCarbohydrate' => $logentries->sum('carbohydrate'),
            'totalPotassium' => $logentries->sum('potassium'),
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
            ->favouritesFilter($request->query('favouritesFilter'))
            ->with('ingredients')
            ->paginate(Config::get('ml2.paginator.per_page'));

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
            ->favouritesFilter($request->query('favouritesFilter'))
            ->with('ingredients')
            ->paginate(Config::get('ml2.paginator.per_page'));

        return Inertia::render('Logentries/Edit',[
            'logentry' => new LogentryResource($logentry),
            'foods' => FoodResource::collection($foods),
            'foodgroups' => FoodgroupResource::collection($foodgroups),
        ]);
    }

    public function update(UpdateLogentryRequest $request, Logentry $logentry)
    {
        $logentry->update($request->validated());
        return redirect()->route('logentries.index');
    }

    public function destroy(Logentry $logentry)
    {
        $logentry->delete();
        return redirect()->route('logentries.index');
    }
}
