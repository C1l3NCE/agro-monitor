@extends('layouts.admin')

@section('header', 'История анализов')

@section('content')

    <!-- ФИЛЬТР -->
    <div
        class="bg-white/70 backdrop-blur-md
                            rounded-3xl shadow-lg border border-gray-200
                            p-6 mb-8">

        <form method="GET" class="flex flex-wrap gap-6 items-end">

            <div class="flex flex-col">
                <label class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Поле
                </label>
                <select name="field_id"
                    class="px-4 py-2 rounded-xl border border-gray-200
                                           focus:ring-2 focus:ring-green-400 text-sm transition">
                    <option value="">Все</option>
                    @foreach ($fields as $field)
                        <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>
                            {{ $field->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Тип анализа
                </label>
                <select name="type"
                    class="px-4 py-2 rounded-xl border border-gray-200
                                           focus:ring-2 focus:ring-green-400 text-sm transition">
                    <option value="">Все</option>
                    <option value="insects" @selected(request('type') == 'insects')>
                        Насекомые
                    </option>
                    <option value="forecast" @selected(request('type') == 'forecast')>
                        Прогноз ИИ
                    </option>
                </select>
            </div>

            <button
                class="px-6 py-2 rounded-xl
                                       bg-gradient-to-r from-green-500 to-green-600
                                       hover:from-green-600 hover:to-green-700
                                       text-white text-sm font-semibold
                                       shadow-md hover:shadow-green-500/30
                                       transition">
                Применить
            </button>

        </form>
    </div>


    <!-- ТАБЛИЦА -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">

        @php $type = request('type'); @endphp

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="p-4 text-left">Дата</th>
                    <th class="p-4 text-left">Поле</th>
                    <th class="p-4 text-left">Тип</th>

                    @if ($type === 'insects')
                        <th class="p-4 text-left">Насекомое</th>
                        <th class="p-4 text-left">Категория</th>
                    @elseif($type === 'forecast')
                        <th class="p-4 text-left">Прогноз</th>
                        <th class="p-4 text-left">Риск</th>
                    @else
                        <th class="p-4 text-left">Результат</th>
                        <th class="p-4 text-left">Категория</th>
                    @endif
                </tr>
            </thead>

            <tbody class="text-gray-700">

                @forelse($analyses as $analysis)

                    @php $result = $analysis->result; @endphp

                    <tr class="border-b last:border-0
                                                       hover:bg-gray-50
                                                       transition duration-200 cursor-pointer"
                        onclick="openAnalysis({{ $analysis->id }})">

                        <!-- Дата -->
                        <td class="p-4 whitespace-nowrap">
                            {{ $analysis->created_at->format('d.m.Y H:i') }}
                        </td>

                        <!-- Поле -->
                        <td class="p-4 font-medium">
                            {{ $analysis->field->name ?? '—' }}
                        </td>

                        <!-- Тип -->
                        <td class="p-4">
                            @if ($analysis->type === 'forecast')
                                <span
                                    class="px-3 py-1 text-xs font-medium
                                                                                     bg-blue-50 text-blue-600 rounded-full">
                                    Прогноз
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 text-xs font-medium
                                                                                     bg-green-50 text-green-600 rounded-full">
                                    Насекомые
                                </span>
                            @endif
                        </td>

                        <!-- Результат -->
                        <td class="p-4">
                            @if ($analysis->type === 'forecast')
                                Прогноз состояния поля
                            @else
                                {{ $result['name'] ?? '—' }}
                            @endif
                        </td>

                        <!-- Категория / Риск -->
                        <td class="p-4">

                            @if ($analysis->type === 'forecast')
                                @php $risk = $result['risk_level'] ?? null; @endphp

                                @if ($risk === 'высокий')
                                    <span class="text-red-600 font-semibold">Высокий риск</span>
                                @elseif($risk === 'средний')
                                    <span class="text-yellow-600 font-semibold">Средний риск</span>
                                @elseif($risk === 'низкий')
                                    <span class="text-green-600 font-semibold">Низкий риск</span>
                                @else
                                    —
                                @endif
                            @else
                                @if (($result['category'] ?? '') === 'pest')
                                    <span class="text-red-600 font-semibold">Вредитель</span>
                                @elseif(($result['category'] ?? '') === 'beneficial')
                                    <span class="text-green-600 font-semibold">Полезное</span>
                                @else
                                    <span class="text-gray-600">Нейтральное</span>
                                @endif
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            Анализов пока нет
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>


    <!-- MODAL -->
    <div id="analysisModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm
                            hidden items-center justify-center z-50">

        <div
            class="bg-white/95 backdrop-blur-md
                                w-full max-w-2xl p-8 rounded-3xl
                                shadow-2xl relative">

            <button onclick="closeModal()"
                class="absolute top-4 right-4
                                       text-gray-400 hover:text-gray-700 transition">
                ✖
            </button>

            <div id="modalContent" class="text-gray-700"></div>

        </div>
    </div>

    <script>
        const analyses = @json($analyses);

        function formatKey(key) {

            const dictionary = {
                name: "Название",
                description: "Описание",
                category: "Категория",
                danger_level: "Уровень опасности",
                control_methods: "Методы борьбы",
                recommendations: "Рекомендации",
                impact: "Воздействие",
                harms: "Вред",
                benefits: "Польза",
                spread_method: "Способ распространения",
                symptoms: "Симптомы",
                prevention: "Профилактика",
                risk_level: "Уровень риска",
                yield_risk: "Риск урожая",
                trend: "Тренд",
                overall_status: "Общее состояние"
            };

            return dictionary[key] || key
                .replace(/_/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());
        }

        function formatValue(value) {

            const valueDictionary = {
                pest: "Вредитель",
                beneficial: "Полезное",
                neutral: "Нейтральное",

                high: "Высокий",
                medium: "Средний",
                low: "Низкий",

                высокий: "Высокий",
                средний: "Средний",
                низкий: "Низкий"
            };

            if (typeof value === 'string') {
                const lower = value.toLowerCase();
                if (valueDictionary[lower]) {
                    return valueDictionary[lower];
                }
            }

            return value;
        }

        function openAnalysis(id) {

            const analysis = analyses.find(a => a.id === id);
            if (!analysis) return;

            const result = analysis.result || {};
            const modal = document.getElementById('analysisModal');
            const content = document.getElementById('modalContent');

            let html = '';

            const title = result.name ? result.name : 'Анализ';

            html += `
            <h2 class="text-2xl font-bold mb-4">
                ${title}
            </h2>
        `;

            if (analysis.image_path) {
                html += `
                <img src="/storage/${analysis.image_path}"
                     class="w-full mb-6 rounded-2xl shadow">
            `;
            }

            if (result.description) {
                html += `
                <p class="mb-6 text-gray-700">
                    ${result.description}
                </p>
            `;
            }

            Object.entries(result).forEach(([key, value]) => {

                if (!value) return;
                if (key === 'name' || key === 'description') return;

                // массив
                if (Array.isArray(value)) {

                    html += `
                    <h3 class="font-semibold mt-6 mb-2">
                        ${formatKey(key)}
                    </h3>
                    <ul class="list-disc ml-6 space-y-1">
                        ${value.map(i => `<li>${formatValue(i)}</li>`).join('')}
                    </ul>
                `;
                }

                // объект
                else if (typeof value === 'object') {

                    html += `
                    <h3 class="font-semibold mt-6 mb-2">
                        ${formatKey(key)}
                    </h3>
                `;

                    Object.entries(value).forEach(([subKey, subVal]) => {

                        if (!subVal) return;

                        if (Array.isArray(subVal)) {
                            html += `
                            <h4 class="font-medium mt-3">
                                ${formatKey(subKey)}
                            </h4>
                            <ul class="list-disc ml-6 space-y-1">
                                ${subVal.map(i => `<li>${formatValue(i)}</li>`).join('')}
                            </ul>
                        `;
                        } else {
                            html += `
                            <p class="mt-2">
                                <strong>${formatKey(subKey)}:</strong>
                                ${formatValue(subVal)}
                            </p>
                        `;
                        }
                    });
                }

                // обычное значение
                else {

                    html += `
                    <p class="mt-4">
                        <strong>${formatKey(key)}:</strong>
                        ${formatValue(value)}
                    </p>
                `;
                }
            });

            content.innerHTML = html;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('analysisModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        document.getElementById('analysisModal').addEventListener('click', function(e) {
            if (e.target.id === 'analysisModal') {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                closeModal();
            }
        });
    </script>

@endsection
