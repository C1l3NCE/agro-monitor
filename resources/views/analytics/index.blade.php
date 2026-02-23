@extends('layouts.admin')

@section('title', 'Аналитика')
@section('header', 'Аналитика полей')

@section('content')

    <!-- ФИЛЬТР -->
    <div class="bg-white/70 backdrop-blur-md p-6 rounded-3xl
                shadow-lg border border-gray-200 mb-10">

        <form method="GET" class="flex flex-wrap gap-6 items-end">

            <div class="flex flex-col">
                <label class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Выберите поле
                </label>

                <select name="field_id" class="px-4 py-2 rounded-xl
                               border border-gray-200
                               bg-white focus:ring-2 focus:ring-green-400
                               text-sm transition">
                    <option value="">Все поля</option>

                    @foreach($allFields as $field)
                        <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>
                            {{ $field->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="px-6 py-2 rounded-xl
                           bg-gradient-to-r from-green-500 to-green-600
                           hover:from-green-600 hover:to-green-700
                           text-white text-sm font-semibold
                           shadow-md hover:shadow-green-500/30
                           transition duration-300">
                Применить
            </button>

        </form>

    </div>

    <!-- ГРАФИКИ -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">

        <!-- Площадь -->
        <div class="bg-white p-8 rounded-3xl
                    shadow-lg border border-gray-100
                    hover:shadow-2xl transition duration-300">

            <h2 class="text-lg font-semibold text-gray-800 mb-6">
                Площадь: план и факт
            </h2>

            <div class="h-[340px]">
                <canvas id="areaChart"></canvas>
            </div>

        </div>

        <!-- NDVI -->
        <div class="bg-white p-8 rounded-3xl
                    shadow-lg border border-gray-100
                    hover:shadow-2xl transition duration-300">

            <h2 class="text-lg font-semibold text-gray-800 mb-6">
                Динамика NDVI
            </h2>

            <div class="h-[340px]">
                <canvas id="ndviChart"></canvas>
            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

    <script>

        const fields = @json($fields);
        const ndviHistory = @json($ndviHistory);

        // -------- ПЛОЩАДИ --------

        new Chart(document.getElementById('areaChart'), {
            type: 'bar',
            data: {
                labels: fields.map(f => f.name),
                datasets: [
                    {
                        label: 'Введённая площадь',
                        data: fields.map(f => f.area),
                        backgroundColor: '#16a34a'
                    },
                    {
                        label: 'Рассчитанная площадь',
                        data: fields.map(f => f.calculated_area ?? 0),
                        backgroundColor: '#2563eb'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // -------- NDVI --------

        const grouped = {};

        ndviHistory.forEach(item => {
            if (!grouped[item.field_id]) {
                grouped[item.field_id] = [];
            }

            grouped[item.field_id].push({
                x: new Date(item.created_at),
                y: parseFloat(item.ndvi)
            });
        });

        const colors = ['#16a34a', '#2563eb', '#9333ea', '#facc15', '#dc2626'];

        let i = 0;

        const datasets = Object.keys(grouped).map(fieldId => {

            const field = fields.find(f => f.id == fieldId);
            const color = colors[i++ % colors.length];

            return {
                label: field ? field.name : 'Поле',
                data: grouped[fieldId],
                borderColor: color,
                backgroundColor: color + '22',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                borderWidth: 2
            };
        });

        new Chart(document.getElementById('ndviChart'), {
            type: 'line',
            data: { datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: { unit: 'day' }
                    },
                    y: {
                        min: 0,
                        max: 1,
                        ticks: { stepSize: 0.1 }
                    }
                }
            }
        });

    </script>

@endsection