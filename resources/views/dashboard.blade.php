@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Панель мониторинга')

@section('content')

@if($fieldsCount === 0)

    <div class="bg-white p-14 rounded-3xl shadow-xl border border-gray-100 text-center
                hover:shadow-2xl transition duration-300">

        <div class="text-4xl mb-4">🚜</div>

        <div class="text-2xl font-semibold text-gray-800 mb-3">
            Полей пока нет
        </div>

        <div class="text-gray-500 mb-8">
            Добавьте первое поле, чтобы начать мониторинг
        </div>

        <a href="{{ route('fields.create') }}"
           class="inline-flex items-center gap-2
                  bg-gradient-to-r from-green-500 to-green-600
                  hover:from-green-600 hover:to-green-700
                  text-white px-8 py-3 rounded-xl
                  shadow-lg hover:shadow-green-500/40
                  font-semibold transition duration-300">
            ➕ Добавить поле
        </a>

    </div>

@else

    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        @foreach([
            ['label'=>'Всего полей','value'=>$fieldsCount,'color'=>'green'],
            ['label'=>'Общая площадь','value'=>$totalArea.' га','color'=>'blue'],
            ['label'=>'Средняя площадь','value'=>$avgArea.' га','color'=>'purple'],
        ] as $card)

        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100
                    hover:-translate-y-1 hover:shadow-2xl
                    transition duration-300">

            <div class="text-gray-500 text-sm uppercase tracking-wide">
                {{ $card['label'] }}
            </div>

            <div class="text-4xl font-bold mt-3 text-gray-800">
                {{ $card['value'] }}
            </div>

        </div>

        @endforeach

    </div>

    <!-- NDVI -->
    <div class="bg-white p-10 rounded-3xl shadow-lg border border-gray-100 mb-12">

        <h2 class="text-xl font-semibold mb-8 text-gray-800">
            🌿 Состояние полей (NDVI)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="p-8 rounded-2xl bg-gradient-to-br from-red-50 to-red-100
                        border border-red-200 hover:shadow-lg transition">
                <div class="text-red-600 text-sm uppercase">Проблемные</div>
                <div class="text-4xl font-bold text-red-700 mt-3">
                    {{ $ndviRed }}
                </div>
            </div>

            <div class="p-8 rounded-2xl bg-gradient-to-br from-yellow-50 to-yellow-100
                        border border-yellow-200 hover:shadow-lg transition">
                <div class="text-yellow-600 text-sm uppercase">Средние</div>
                <div class="text-4xl font-bold text-yellow-700 mt-3">
                    {{ $ndviYellow }}
                </div>
            </div>

            <div class="p-8 rounded-2xl bg-gradient-to-br from-green-50 to-green-100
                        border border-green-200 hover:shadow-lg transition">
                <div class="text-green-600 text-sm uppercase">Хорошие</div>
                <div class="text-4xl font-bold text-green-700 mt-3">
                    {{ $ndviGreen }}
                </div>
            </div>

        </div>

    </div>

    <!-- Основной блок -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">

        <!-- Последние поля -->
        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 xl:col-span-2">

            <h2 class="font-semibold text-xl mb-8 text-gray-800">
                📋 Последние поля
            </h2>

            <ul class="divide-y divide-gray-100">

                @foreach($recentFields as $field)
                    <li class="py-5 flex justify-between items-center
                               hover:bg-gray-50 px-4 rounded-xl transition">

                        <div>
                            <div class="font-semibold text-gray-800">
                                {{ $field->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $field->crop }} · {{ $field->area }} га
                            </div>
                        </div>

                        <a href="{{ route('fields.draw', $field) }}"
                           class="text-green-600 hover:text-green-800 text-sm font-semibold transition">
                            Контур →
                        </a>

                    </li>
                @endforeach

            </ul>

            <a href="{{ route('fields.index') }}"
               class="inline-block mt-8 text-green-600 hover:underline text-sm font-medium">
                Смотреть все поля →
            </a>

        </div>

        <!-- Мини карта -->
        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">

            <h2 class="font-semibold text-xl mb-6 text-gray-800">
                🗺 Карта
            </h2>

            <div class="h-72 rounded-2xl overflow-hidden shadow-inner">
                <iframe src="{{ route('map.embed') }}"
                        class="w-full h-full border-0">
                </iframe>
            </div>

            <a href="{{ route('map.index') }}"
               class="inline-block mt-6 text-green-600 hover:underline text-sm font-medium">
                Открыть карту →
            </a>

        </div>

    </div>

    <!-- ИИ блок -->
    @if(auth()->user()->hasRole(['admin','manager','agronom']))

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-14">

            <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100
                        hover:shadow-xl transition">
                <div class="text-gray-500 text-sm uppercase">
                    Всего ИИ-анализов
                </div>
                <div class="text-4xl font-bold mt-3 text-gray-800">
                    {{ $totalAnalyses }}
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100
                        hover:shadow-xl transition">
                <div class="text-gray-500 text-sm uppercase">
                    Обнаружено вредителей
                </div>
                <div class="text-4xl font-bold mt-3 text-red-600">
                    {{ $pestCount }}
                </div>
            </div>

        </div>

    @endif

@endif

@endsection