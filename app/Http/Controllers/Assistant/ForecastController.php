<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\AiAnalysis;
use App\Services\GptForecastService;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $fields = $user->isAdmin()
            ? Field::all()
            : Field::where('user_id', $user->id)->get();

        return view('assistant.forecast', compact('fields'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id'
        ]);

        $field = Field::findOrFail($request->field_id);

        // История NDVI
        $ndviHistory = DB::table('field_ndvi_history')
            ->where('field_id', $field->id)
            ->orderBy('created_at')
            ->get(['ndvi', 'created_at']);

        // Последние анализы
        $recentAnalyses = AiAnalysis::where('field_id', $field->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($a) {
                return [
                    'name' => $a->result['name'] ?? null,
                    'category' => $a->result['category'] ?? null,
                    'danger_level' => $a->result['danger_level'] ?? null,
                    'date' => $a->created_at,
                ];
            });

        $payload = [
            'field_name' => $field->name,
            'crop' => $field->crop,
            'area' => $field->area,
            'current_ndvi' => $field->ndvi_avg,
            'ndvi_history' => $ndviHistory,
            'recent_analyses' => $recentAnalyses,
        ];

        $forecast = GptForecastService::generate($payload);

        AiAnalysis::create([
            'user_id' => auth()->id(),
            'field_id' => $field->id,
            'type' => 'forecast',
            'image_path' => null,
            'result' => $forecast,
        ]);

        return back()->with('forecast', $forecast);
    }
}
