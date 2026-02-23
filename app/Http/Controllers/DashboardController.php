<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\AiAnalysis;
use App\Services\AiRecommendationService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Только агроном ограничен
        $isAgronom = $user->isAgronom();

        $fields = $isAgronom
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        return $this->buildDashboard($fields, $isAgronom);
    }

    /*
    |--------------------------------------------------------------------------
    | SHARED DASHBOARD BUILDER
    |--------------------------------------------------------------------------
    */

    private function buildDashboard($fields, $isAgronom = false)
    {
        $fieldsCount = $fields->count();
        $totalArea = $fields->sum('area');
        $avgArea = $fieldsCount ? round($totalArea / $fieldsCount, 2) : 0;

        // Query для NDVI
        $fieldsQuery = $isAgronom
            ? Field::where('user_id', auth()->id())
            : Field::query();

        $ndviRed = (clone $fieldsQuery)
            ->where('ndvi_avg', '<', 0.3)
            ->count();

        $ndviYellow = (clone $fieldsQuery)
            ->whereBetween('ndvi_avg', [0.3, 0.6])
            ->count();

        $ndviGreen = (clone $fieldsQuery)
            ->where('ndvi_avg', '>', 0.6)
            ->count();

        $recentFields = $fields->sortByDesc('id')->take(5)->values();

        // AI рекомендации
        $recommendations = AiRecommendationService::generate($fields);

        // Анализы
        $analysesQuery = $isAgronom
            ? AiAnalysis::where('user_id', auth()->id())
            : AiAnalysis::query();

        $totalAnalyses = $analysesQuery->count();

        $pestCount = (clone $analysesQuery)
            ->whereJsonContains('result->category', 'pest')
            ->count();

        $recentAnalyses = (clone $analysesQuery)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'fieldsCount',
            'totalArea',
            'avgArea',
            'ndviRed',
            'ndviYellow',
            'ndviGreen',
            'recommendations',
            'recentFields',
            'totalAnalyses',
            'pestCount',
            'recentAnalyses'
        ));
    }
}
