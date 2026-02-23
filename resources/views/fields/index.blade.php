@extends('layouts.admin')

@section('title', 'Поля')
@section('header', 'Сельскохозяйственные поля')

@section('content')

@if(auth()->user()->hasRole(['admin', 'manager']))
    <div class="mb-8">
        <a href="{{ route('fields.create') }}"
           class="inline-flex items-center justify-center
                  px-6 py-3 rounded-xl
                  bg-gradient-to-r from-green-500 to-green-600
                  hover:from-green-600 hover:to-green-700
                  text-white font-semibold
                  shadow-md hover:shadow-green-500/30
                  transition">
            Добавить поле
        </a>
    </div>
@endif


<div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">

    <table class="w-full text-sm text-gray-700">

        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-6 py-4 text-left">Поле</th>
                <th class="px-6 py-4 text-left">Культура</th>
                <th class="px-6 py-4 text-left">Площадь</th>
                <th class="px-6 py-4 text-left">NDVI</th>
                <th class="px-6 py-4 text-left">Координаты</th>
                <th class="px-6 py-4 text-left">Действия</th>
            </tr>
        </thead>

        <tbody>

        @forelse($fields as $field)

            <tr class="border-t hover:bg-gray-50 transition duration-200">

                <!-- Название -->
                <td class="px-6 py-5 font-semibold text-gray-900">
                    {{ $field->name }}
                </td>

                <!-- Культура -->
                <td class="px-6 py-5">
                    <span class="px-3 py-1 text-xs font-medium
                                 bg-green-50 text-green-600
                                 rounded-full">
                        {{ $field->crop }}
                    </span>
                </td>

                <!-- Площадь -->
                <td class="px-6 py-5">
                    <div class="text-sm">
                        <div class="font-medium text-gray-800">
                            {{ $field->area }} га
                        </div>
                        <div class="text-gray-400 text-xs">
                            расчёт: {{ $field->calculated_area ?? '—' }} га
                        </div>
                    </div>
                </td>

                <!-- NDVI -->
                <td class="px-6 py-5">

                    @if($field->ndvi_avg !== null)

                        @php
                            $ndvi = $field->ndvi_avg;

                            if ($ndvi < 0.3) {
                                $color = 'bg-red-500';
                                $text = 'text-red-600';
                                $label = 'Низкий';
                            } elseif ($ndvi < 0.6) {
                                $color = 'bg-yellow-400';
                                $text = 'text-yellow-600';
                                $label = 'Средний';
                            } else {
                                $color = 'bg-green-500';
                                $text = 'text-green-600';
                                $label = 'Высокий';
                            }

                            $percent = min(max($ndvi * 100, 0), 100);
                        @endphp

                        <div class="space-y-2">

                            <div class="flex justify-between text-xs font-medium {{ $text }}">
                                <span>{{ $ndvi }}</span>
                                <span>{{ $label }}</span>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full transition-all duration-500"
                                     style="width: {{ $percent }}%">
                                </div>
                            </div>

                        </div>

                    @else
                        <span class="text-gray-400 text-xs">
                            Нет данных
                        </span>
                    @endif

                </td>

                <!-- Координаты -->
                <td class="px-6 py-5 text-xs text-gray-500">
                    {{ $field->latitude }},
                    {{ $field->longitude }}
                </td>

                <!-- Действия -->
                <td class="px-6 py-5">

                    <div class="flex gap-4 text-sm font-medium">

                        <a href="{{ route('fields.draw', $field) }}"
                           class="text-green-600 hover:text-green-800 transition">
                            Контур
                        </a>

                        <a href="{{ route('assistant.forecast') }}"
                           class="text-blue-600 hover:text-blue-800 transition">
                            Прогноз
                        </a>

                    </div>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    Полей пока нет
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

</div>


<script>
function calcNdvi(fieldId) {
    fetch(`/fields/${fieldId}/ndvi`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => {
        if (!response.ok) {
            alert('Ошибка при расчёте NDVI');
        } else {
            location.reload();
        }
    });
}
</script>

@endsection
