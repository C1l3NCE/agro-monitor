@extends('layouts.admin')

@section('header', 'Насекомые')

@section('content')

    <div class="max-w-4xl space-y-8">

        <!-- ФОРМА -->
        <form method="POST" action="{{ route('assistant.insects.analyze') }}" enctype="multipart/form-data" class="bg-white/80 backdrop-blur-md
                             p-8 rounded-3xl shadow-lg
                             border border-gray-200 space-y-6">

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

            <div>
                <label class="text-xs uppercase tracking-wide text-gray-500 mb-2 block">
                    Фотография насекомого
                </label>

                <input type="file" name="image" accept="image/*" required class="w-full px-4 py-3 rounded-xl
                                      border border-gray-200
                                      bg-white
                                      focus:ring-2 focus:ring-green-400
                                      transition text-sm" onchange="previewImage(event)">
            </div>

            <!-- ПРЕВЬЮ -->
            <div id="preview" class="hidden bg-gray-50 p-4 rounded-2xl border border-gray-100">

                <p class="text-xs uppercase text-gray-500 mb-3">
                    Превью изображения
                </p>

                <img id="preview-img" class="max-h-72 rounded-2xl shadow-md mx-auto">
            </div>

            <button class="w-full py-3 rounded-xl
                               bg-gradient-to-r from-green-500 to-green-600
                               hover:from-green-600 hover:to-green-700
                               text-white font-semibold tracking-wide
                               shadow-lg hover:shadow-green-500/30
                               transition duration-300">
                Анализировать
            </button>

        </form>


        <!-- РЕЗУЛЬТАТ -->
        @if($analysis)

            @php $result = $analysis->result; @endphp

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 space-y-6">

                <h2 class="text-2xl font-semibold text-gray-800">
                    Результат анализа
                </h2>

                <!-- Название -->
                <div>
                    <span class="text-sm text-gray-500 uppercase tracking-wide">
                        Название
                    </span>
                    <div class="text-lg font-semibold text-gray-800 mt-1">
                        {{ $result['name'] ?? '—' }}
                    </div>
                </div>

                <!-- Опасность -->
                <div>
                    <span class="text-sm text-gray-500 uppercase tracking-wide">
                        Уровень опасности
                    </span>

                    @php
                        $danger = $result['danger_level'] ?? '';
                    @endphp

                    <div class="mt-2">
                        @if($danger === 'высокий')
                            <span class="px-3 py-1 text-sm font-medium bg-red-50 text-red-600 rounded-full">
                                Высокий
                            </span>
                        @elseif($danger === 'средний')
                            <span class="px-3 py-1 text-sm font-medium bg-yellow-50 text-yellow-600 rounded-full">
                                Средний
                            </span>
                        @elseif($danger === 'низкий')
                            <span class="px-3 py-1 text-sm font-medium bg-green-50 text-green-600 rounded-full">
                                Низкий
                            </span>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <!-- Описание -->
                <div class="text-gray-700 leading-relaxed">
                    {{ $result['description'] ?? '' }}
                </div>

                <!-- Вред -->
                @if(!empty($result['impact']['harms']))
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Вред</h3>
                        <ul class="list-disc ml-6 space-y-1 text-gray-700">
                            @foreach($result['impact']['harms'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Польза -->
                @if(!empty($result['impact']['benefits']))
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Польза</h3>
                        <ul class="list-disc ml-6 space-y-1 text-gray-700">
                            @foreach($result['impact']['benefits'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Методы борьбы -->
                @if(!empty($result['control_methods']))
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Методы борьбы</h3>
                        <ul class="list-disc ml-6 space-y-1 text-gray-700">
                            @foreach($result['control_methods'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Рекомендации -->
                @if(!empty($result['recommendations']))
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Рекомендации</h3>
                        <ul class="list-disc ml-6 space-y-1 text-gray-700">
                            @foreach($result['recommendations'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>

        @endif

    </div>


    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const img = document.getElementById('preview-img');

            img.src = URL.createObjectURL(event.target.files[0]);
            preview.classList.remove('hidden');
        }
    </script>

@endsection