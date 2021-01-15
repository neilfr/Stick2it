<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogentryRequest;
use Inertia\Inertia;
use App\Models\Logentry;
use Illuminate\Http\Request;
use App\Http\Resources\LogentryResource;

class LogentryController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Logentries/Index',[
            'logentries' => LogentryResource::collection(Logentry::where('user_id', auth()->user()->id)->get())
        ]);
    }

    public function store(StoreLogentryRequest $request)
    {
        Logentry::create($request->validated());

        return redirect()->route('logentries.index');
    }
}
