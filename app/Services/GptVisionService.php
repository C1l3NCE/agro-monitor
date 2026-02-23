<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GptVisionService
{
    public static function analyzeInsect(string $base64Image): array
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'temperature' => 0.2,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' =>
                            'Ты профессиональный агроном-энтомолог.
Отвечай СТРОГО НА РУССКОМ ЯЗЫКЕ.
Проанализируй изображение насекомого.

Определи, относится ли оно к одной из категорий:
- вредитель (pest)
- полезное насекомое (beneficial)
- нейтральное (neutral)

Верни ТОЛЬКО корректный JSON без текста вне JSON.

Структура ответа:

{
  "name": "название на русском",
  "category": "pest|beneficial|neutral",
  "danger_level": "none|low|medium|high",
  "description": "краткое описание",

  "impact": {
    "harms": ["конкретный вред (если есть)"],
    "benefits": ["конкретная польза (если есть)"]
  },

  "control_methods": [
    "методы борьбы (только если pest)"
  ],

  "recommendations": [
    "конкретные рекомендации агроному, название лекарства(если нужно)"
  ]
}

Правила:
- Если насекомое полезное — ОБЯЗАТЕЛЬНО заполни benefits и оставь harms пустым массивом.
- Если вредитель — заполни harms и control_methods.
- Если нейтральное — оба массива могут быть пустыми.
- Если не наносит вреда — danger_level = "none".
- Все массивы должны присутствовать, даже если пустые.
- Никакого текста вне JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Ошибка GPT API');
        }

        $content = $response->json('choices.0.message.content');

        // Убираем мусор, если ИИ всё-таки что-то написал
        $json = trim($content);
        $json = preg_replace('/^```json|```$/', '', $json);

        return json_decode($json, true);
    }
}
