<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GptVisionService;
use App\Models\AiAnalysis;
use Illuminate\Support\Facades\Storage;
use App\Models\Field;

class InsectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $fields = $user->isAdmin()
            ? Field::all()
            : Field::where('user_id', $user->id)->get();

        $analysis = null;

        if ($request->analysis) {
            $analysis = AiAnalysis::find($request->analysis);
        }

        return view('assistant.insects', compact('fields', 'analysis'));
    }
    public function analyze(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'image' => 'required|image|max:5120',
        ]);

        $image = $request->file('image');

        //  Сохраняем изображение
        $path = $image->store('ai/insects', 'public');

        //  Подготавливаем base64 для GPT
        $base64 = base64_encode(file_get_contents($image->getRealPath()));

        try {
            $result = GptVisionService::analyzeInsect($base64);
        } catch (\Exception $e) {
            return back()->withErrors([
                'image' => 'Ошибка анализа изображения'
            ]);
        }

        // 3️⃣ Сохраняем анализ
        $analysis = AiAnalysis::create([
            'user_id' => auth()->id(),
            'field_id' => $request->field_id,
            'type' => 'insects',
            'image_path' => $path,
            'result' => $result,
        ]);

        // ===============================
        // ЛОГИКА NDVI
        // ===============================

        $field = Field::find($request->field_id);

        // Все анализы этого поля
        $analyses = AiAnalysis::where('field_id', $field->id)->get();

        $pestCount = $analyses->filter(function ($a) {
            return ($a->result['category'] ?? '') === 'pest';
        })->count();

        $total = $analyses->count();

        $ndvi = $field->ndvi_avg ?? 0.75;

        // Если вредитель — снижаем
        if (($result['category'] ?? '') === 'pest') {
            $ndvi -= 0.1;
        }

        // Если полезное — слегка улучшаем
        if (($result['category'] ?? '') === 'beneficial') {
            $ndvi += 0.05;
        }

        // Ограничиваем диапазон
        $ndvi = max(0.1, min(0.9, $ndvi));
        $ndvi = round($ndvi, 2);
        // Формируем зоны
        if ($ndvi < 0.3) {
            $zones = [
                [
                    'value' => $ndvi,
                    'color' => '#dc2626',
                    'label' => 'Проблемная зона'
                ]
            ];
        } elseif ($ndvi < 0.6) {
            $zones = [
                [
                    'value' => $ndvi,
                    'color' => '#facc15',
                    'label' => 'Среднее состояние'
                ]
            ];
        } else {
            $zones = [
                [
                    'value' => $ndvi,
                    'color' => '#16a34a',
                    'label' => 'Хорошее состояние'
                ]
            ];
        }

        // Обновляем поле
        $field->update([
            'ndvi_avg' => $ndvi,
            'ndvi_zones' => json_encode($zones),
        ]);
        \DB::table('field_ndvi_history')->insert([
            'field_id' => $field->id,
            'ndvi' => $ndvi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('assistant.insects.index', [
                'analysis' => $analysis->id
            ]);
    }
    public function history(Request $request)
    {
        $user = auth()->user();

        $query = AiAnalysis::with('field');

        // Только агроном ограничен
        if ($user->isAgronom()) {
            $query->where('user_id', $user->id);
        }

        // Фильтр по полю
        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        // Фильтр по типу
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $analyses = $query->latest()->get();

        $fields = $user->isAgronom()
            ? Field::where('user_id', $user->id)->get()
            : Field::all();

        return view('assistant.history', compact('analyses', 'fields'));
    }
}
