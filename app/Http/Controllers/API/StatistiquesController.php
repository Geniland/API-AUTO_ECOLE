<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Cours;
use App\Models\Test;
use App\Models\Video;
use App\Models\User;

class StatistiquesController extends Controller
{
    public function index()
    {
        return response()->json([
            'cours' => Cours::count(),
            'exercices' => Test::count(),
            'videos' => Video::count(),
            'users' => User::count(),
        ]);
    }
}