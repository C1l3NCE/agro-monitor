@extends('layouts.admin')

@section('title', 'Добавить поле')
@section('header', 'Добавление поля')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="bg-white/90 backdrop-blur-md
                shadow-xl rounded-3xl
                p-10 border border-gray-200">

        <form method="POST" action="{{ route('fields.store') }}" class="space-y-8">
            @csrf

            {{-- Ошибки --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200
                            text-red-700 p-5 rounded-2xl">
                    <ul class="list-disc ml-6 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Основные данные --}}
            <div class="grid md:grid-cols-2 gap-8">

                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Название поля
                    </label>
                    <input name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Например: Поле №7"
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Культура
                    </label>
                    <input name="crop"
                           value="{{ old('crop') }}"
                           required
                           placeholder="Пшеница, Кукуруза..."
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

            </div>


            {{-- Площадь --}}
            <div>
                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Площадь (га)
                </label>
                <input name="area"
                       type="number"
                       step="0.01"
                       value="{{ old('area') }}"
                       required
                       class="w-full px-4 py-3 rounded-xl
                              border border-gray-200
                              focus:ring-2 focus:ring-green-400
                              transition text-sm">
            </div>


            {{-- Координаты --}}
            <div class="grid md:grid-cols-2 gap-8">

                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Широта
                    </label>
                    <input name="latitude"
                           type="number"
                           step="0.0000001"
                           value="{{ old('latitude') }}"
                           required
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Долгота
                    </label>
                    <input name="longitude"
                           type="number"
                           step="0.0000001"
                           value="{{ old('longitude') }}"
                           required
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

            </div>


            {{-- Описание --}}
            <div>
                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Описание
                </label>
                <textarea name="description"
                          rows="4"
                          placeholder="Дополнительная информация о поле..."
                          class="w-full px-4 py-3 rounded-xl
                                 border border-gray-200
                                 focus:ring-2 focus:ring-green-400
                                 transition text-sm">{{ old('description') }}</textarea>
            </div>


            {{-- Кнопки --}}
            <div class="flex justify-between items-center pt-6 border-t border-gray-100">

                <a href="{{ route('fields.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-800 transition">
                    Назад
                </a>

                <button type="submit"
                        class="px-8 py-3 rounded-xl
                               bg-gradient-to-r from-green-500 to-green-600
                               hover:from-green-600 hover:to-green-700
                               text-white font-semibold
                               shadow-lg hover:shadow-green-500/30
                               transition duration-300">
                    Сохранить поле
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
