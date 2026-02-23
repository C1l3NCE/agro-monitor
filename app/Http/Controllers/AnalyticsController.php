<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\AiAnalysis;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ===== ПОЛЯ =====
        $fieldsQuery = $user->isAgronom()
            ? Field::where('user_id', $user->id)
            : Field::query();

        if ($request->filled('field_id')) {
            $fieldsQuery->where('id', $request->field_id);
        }

        $fields = $fieldsQuery->get();

        $allFields = $user->isAgronom()
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        // ===== NDVI HISTORY =====
        $ndviHistory = DB::table('field_ndvi_history')
            ->whereIn('field_id', $fields->pluck('id'))
            ->orderBy('created_at')
            ->get();

        // ===== АНАЛИЗЫ =====
        $analysesQuery = AiAnalysis::query();

        if ($user->isAgronom()) {
            $analysesQuery->where('user_id', $user->id);
        }

        if ($request->filled('field_id')) {
            $analysesQuery->where('field_id', $request->field_id);
        }

        if ($request->filled('type')) {
            $analysesQuery->where('type', $request->type);
        }

        $analyses = $analysesQuery->latest()->get();

        return view('analytics.index', compact(
            'fields',
            'allFields',
            'ndviHistory',
            'analyses'
        ));
    }
}
