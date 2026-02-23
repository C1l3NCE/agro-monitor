<?php

namespace App\Services;

use App\Models\Field;

class AiRecommendationService
{
    public static function generate($fields)
{
    $recommendations = [];

    foreach ($fields as $field) {

        // 1️⃣ Нет контура
        if (!$field->geometry) {
            $recommendations[] =
                "Поле «{$field->name}»: не задан контур поля. Без контура невозможно провести точный анализ.";
            continue; // дальше нет смысла анализировать
        }

        // 2️⃣ Нет рассчитанной площади
        if (!$field->calculated_area) {
            $recommendations[] =
                "Поле «{$field->name}»: площадь не рассчитана по геометрии. Рекомендуется пересчитать контур.";
        }

        // 3️⃣ Нет NDVI
        if ($field->ndvi_avg === null) {
            $recommendations[] =
                "Поле «{$field->name}»: NDVI ещё не рассчитан. Выполните анализ состояния растительности.";
        }

        // 4️⃣ Низкий NDVI
        if ($field->ndvi_avg !== null && $field->ndvi_avg < 0.4) {
            $recommendations[] =
                "Поле «{$field->name}»: низкий NDVI. Возможен стресс растений или дефицит влаги.";
        }

        // 5️⃣ Расхождение площади
        if ($field->calculated_area &&
            abs($field->area - $field->calculated_area) > 5) {
            $recommendations[] =
                "Поле «{$field->name}»: расхождение между введённой и фактической площадью превышает 5 га.";
        }
    }

    if (empty($recommendations)) {
        $recommendations[] =
            "Все поля заполнены корректно. Критических отклонений не обнаружено.";
    }

    return $recommendations;
}

}
