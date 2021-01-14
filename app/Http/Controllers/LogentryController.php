<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Logentry;
use Illuminate\Http\Request;

class LogentryController extends Controller
{
    public function index()
    {
        $logentries = Logentry::where('user_id', auth()->user()->id)->get();
        return Inertia::render('Logentries/Index',[
            'logentries' => $logentries,
        ]);
    }
}
