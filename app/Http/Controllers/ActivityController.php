<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->paginate(20);

        return view('activity.index', compact('activities'));
    }
}
