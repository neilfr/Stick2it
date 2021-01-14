<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Logentry;
use Illuminate\Http\Request;
use App\Http\Resources\LogentryResource;

class LogentryController extends Controller
{
    public function index()
    {
        return Inertia::render('Logentries/Index',[
            'logentries' => LogentryResource::collection(Logentry::where('user_id', auth()->user()->id)->get())
        ]);
    }
}
