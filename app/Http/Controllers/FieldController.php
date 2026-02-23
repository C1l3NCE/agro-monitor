<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\GptForecastService;
use App\Models\AiAnalysis;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ActivityLog;

class FieldController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Field::class);

        $user = auth()->user();

        $fields = $user->isAgronom()
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        return view('fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Field::class);

        return view('fields.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Field::class);

        $request->validate([
            'name' => 'required|string',
            'crop' => 'required|string',
            'area' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $field = Field::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'crop' => $request->crop,
            'area' => $request->area,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => 'Field',
            'model_id' => $field->id,
            'description' => 'Создано поле: ' . $field->name,
        ]);

        return redirect()->route('fields.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        $this->authorize('view', $field);

        return view('fields.show', compact('field'));
    }

    /**
     * Forecast using GPT
     */
    public function forecast(Field $field)
    {
        $this->authorize('view', $field);

        $ndviHistory = DB::table('field_ndvi_history')
            ->where('field_id', $field->id)
            ->orderBy('created_at')
            ->get(['ndvi', 'created_at']);

        $recentPests = AiAnalysis::where('field_id', $field->id)
            ->where('type', 'insects')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($a) {
                return [
                    'name' => $a->result['name'] ?? null,
                    'category' => $a->result['category'] ?? null,
                    'danger_level' => $a->result['danger_level'] ?? null,
                    'date' => $a->created_at
                ];
            });

        $payload = [
            'field_name' => $field->name,
            'crop' => $field->crop,
            'area' => $field->area,
            'current_ndvi' => $field->ndvi_avg,
            'ndvi_history' => $ndviHistory,
            'recent_analyses' => $recentPests
        ];

        try {
            $forecast = GptForecastService::generate($payload);
        } catch (\Exception $e) {
            return back()->withErrors([
                'forecast' => 'Ошибка формирования прогноза'
            ]);
        }

        return back()->with('forecast', $forecast);
    }

    /**
     * Draw field geometry
     */
    public function draw(Field $field)
    {
        $this->authorize('view', $field);

        return view('fields.draw', compact('field'));
    }

    /**
     * Save geometry
     */
    public function saveGeometry(Request $request, Field $field)
    {
        $this->authorize('update', $field);

        $field->update([
            'geometry' => json_encode($request->geometry),
            'calculated_area' => $request->calculated_area,
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Generate NDVI zones
     */
    public function generateNdvi(Field $field)
    {
        $this->authorize('update', $field);

        if (!$field->geometry) {
            return response()->json(['error' => 'Нет геометрии'], 400);
        }

        $zones = [
            ['value' => 0.25, 'color' => '#dc2626', 'label' => 'Плохое состояние'],
            ['value' => 0.55, 'color' => '#facc15', 'label' => 'Среднее состояние'],
            ['value' => 0.78, 'color' => '#16a34a', 'label' => 'Хорошее состояние'],
        ];

        $field->update([
            'ndvi_zones' => json_encode($zones),
            'ndvi_avg' => 0.53,
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Delete field
     */
    public function destroy(Field $field)
    {
        $this->authorize('delete', $field);

        $field->delete();

        return redirect()->route('fields.index');
    }
}
