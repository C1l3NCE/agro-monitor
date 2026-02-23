@extends('layouts.admin')

@section('header', 'Помощь агроному')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

        <!-- Насекомые -->
        <a href="{{ route('assistant.insects.index') }}" class="group bg-white/80 backdrop-blur-md
                  p-8 rounded-3xl shadow-lg
                  border border-gray-200
                  hover:shadow-2xl hover:-translate-y-1
                  transition duration-300">

            <div class="text-3xl mb-4">
                🐞
            </div>

            <div class="text-lg font-semibold text-gray-800 mb-2">
                Насекомые
            </div>

            <div class="text-gray-500 text-sm leading-relaxed">
                Определение вредителей по фотографии и рекомендации по борьбе
            </div>

        </a>


        <!-- Прогноз ИИ -->
        <a href="{{ route('assistant.forecast') }}" class="group bg-white/80 backdrop-blur-md
                  p-8 rounded-3xl shadow-lg
                  border border-gray-200
                  hover:shadow-2xl hover:-translate-y-1
                  transition duration-300">

            <div class="text-3xl mb-4">
                📊
            </div>

            <div class="text-lg font-semibold text-gray-800 mb-2">
                Прогноз ИИ
            </div>

            <div class="text-gray-500 text-sm leading-relaxed">
                Анализ состояния поля и прогноз на 30 дней
            </div>

        </a>


        <!-- Растения (заглушка) -->
        <div class="bg-gray-50
                    p-8 rounded-3xl
                    border border-gray-200
                    opacity-60 cursor-not-allowed">

            <div class="text-3xl mb-4">
                🌱
            </div>

            <div class="text-lg font-semibold text-gray-700 mb-2">
                Растения
            </div>

            <div class="text-gray-500 text-sm">
                В разработке
            </div>

        </div>


        <!-- Почва (заглушка) -->
        <div class="bg-gray-50
                    p-8 rounded-3xl
                    border border-gray-200
                    opacity-60 cursor-not-allowed">

            <div class="text-3xl mb-4">
                🌍
            </div>

            <div class="text-lg font-semibold text-gray-700 mb-2">
                Почва
            </div>

            <div class="text-gray-500 text-sm">
                В разработке
            </div>

        </div>

    </div>

@endsection