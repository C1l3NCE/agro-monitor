<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GptForecastService
{
    public static function generate(array $data): array
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'temperature' => 0.3,
                'response_format' => [
                    'type' => 'json_object'
                ],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' =>
                            'Ты профессиональный агроном-аналитик с опытом работы в сельском хозяйстве.
Отвечай строго на РУССКОМ языке.
Проанализируй данные поля и сделай обоснованный прогноз на 30 дней.

Ты должен:
1. Проанализировать динамику NDVI
2. Учесть наличие вредителей
3. Оценить риск ухудшения состояния
4. Дать практические рекомендации

Верни ТОЛЬКО JSON без текста вне JSON.

Формат ответа:

{
  "overall_status": "string (краткое общее состояние поля)",
  "risk_level": "низкий|средний|высокий",
  "trend": "улучшение|стабильно|ухудшение",
  "ndvi_forecast": {
    "current": number,
    "expected_in_30_days": number,
    "explanation": "string (почему ожидается такое изменение)"
  },
  "yield_risk": "низкий|средний|высокий",
  "threat_analysis": "string (описание основных рисков)",
  "recommendations": [
    "конкретная агрономическая рекомендация",
    "конкретная агрономическая рекомендация"
  ]
}'
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode($data)
                    ]
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Ошибка GPT');
        }

        $content = $response->json('choices.0.message.content');
        $json = trim(preg_replace('/^```json|```$/', '', $content));

        return json_decode($json, true);
    }
}
