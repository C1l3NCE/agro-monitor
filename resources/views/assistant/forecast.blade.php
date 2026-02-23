@extends('layouts.admin')

@section('header', 'Прогноз ИИ')

@section('content')

    <div class="max-w-4xl space-y-8">

        <!-- ФОРМА -->
        <div class="bg-white/80 backdrop-blur-md
                    p-8 rounded-3xl shadow-lg
                    border border-gray-200">

            <form method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 mb-2 block">
                        Поле
                    </label>

                    <select name="field_id" required class="w-full px-4 py-3 rounded-xl
                                   border border-gray-200
                                   focus:ring-2 focus:ring-green-400
                                   transition text-sm">
                        <option value="">— Выберите поле —</option>

                        @foreach($fields as $field)
                            <option value="{{ $field->id }}">
                                {{ $field->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="w-full py-3 rounded-xl
                           bg-gradient-to-r from-green-500 to-green-600
                           hover:from-green-600 hover:to-green-700
                           text-white font-semibold tracking-wide
                           shadow-lg hover:shadow-green-500/30
                           transition duration-300">
                    Сформировать прогноз
                </button>

            </form>
        </div>


        <!-- РЕЗУЛЬТАТ -->
        @if(session('forecast'))

            @php $forecast = session('forecast'); @endphp

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 space-y-8">

                <h2 class="text-2xl font-semibold text-gray-800">
                    Прогноз на 30 дней
                </h2>

                <!-- Основные показатели -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <div class="text-xs uppercase text-gray-500 mb-2">
                            Общее состояние
                        </div>
                        <div class="font-semibold text-gray-800">
                            {{ $forecast['overall_status'] }}
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <div class="text-xs uppercase text-gray-500 mb-2">
                            Уровень риска
                        </div>

                        @php $risk = $forecast['risk_level']; @endphp

                        <div>
                            @if($risk === 'высокий')
                                <span class="px-3 py-1 text-sm font-medium bg-red-50 text-red-600 rounded-full">
                                    Высокий
                                </span>
                            @elseif($risk === 'средний')
                                <span class="px-3 py-1 text-sm font-medium bg-yellow-50 text-yellow-600 rounded-full">
                                    Средний
                                </span>
                            @elseif($risk === 'низкий')
                                <span class="px-3 py-1 text-sm font-medium bg-green-50 text-green-600 rounded-full">
                                    Низкий
                                </span>
                            @else
                                {{ $risk }}
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl">
                        <div class="text-xs uppercase text-gray-500 mb-2">
                            Тренд
                        </div>
                        <div class="font-semibold text-gray-800">
                            {{ $forecast['trend'] }}
                        </div>
                    </div>

                </div>


                <!-- NDVI -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Прогноз NDVI
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="bg-gray-50 p-6 rounded-2xl">
                            <div class="text-xs uppercase text-gray-500 mb-2">
                                Текущее значение
                            </div>
                            <div class="text-xl font-semibold text-gray-800">
                                {{ $forecast['ndvi_forecast']['current'] ?? 'Нет данных' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-2xl">
                            <div class="text-xs uppercase text-gray-500 mb-2">
                                Через 30 дней
                            </div>
                            <div class="text-xl font-semibold text-gray-800">
                                {{ $forecast['ndvi_forecast']['expected_in_30_days'] ?? 'Нет данных' }}
                            </div>
                        </div>

                    </div>

                    <p class="text-sm text-gray-600">
                        {{ $forecast['ndvi_forecast']['explanation'] ?? '' }}
                    </p>
                </div>


                <!-- Риск урожая -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        Риск урожая
                    </h3>
                    <p class="text-gray-700">
                        {{ $forecast['yield_risk'] }}
                    </p>
                </div>


                <!-- Угрозы -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        Анализ угроз
                    </h3>
                    <p class="text-gray-700">
                        {{ $forecast['threat_analysis'] }}
                    </p>
                </div>


                <!-- Рекомендации -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Рекомендации
                    </h3>

                    <ul class="list-disc ml-6 space-y-1 text-gray-700">
                        @foreach($forecast['recommendations'] ?? [] as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        @endif

    </div>

@endsection