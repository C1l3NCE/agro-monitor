@extends('layouts.admin')

@section('header', 'Журнал активности')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">
                Действия пользователей
            </h2>
            <p class="text-sm text-gray-500">
                История изменений в системе
            </p>
        </div>

        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-6 py-4 text-left">Дата</th>
                    <th class="px-6 py-4 text-left">Пользователь</th>
                    <th class="px-6 py-4 text-left">Действие</th>
                    <th class="px-6 py-4 text-left">Объект</th>
                    <th class="px-6 py-4 text-left">Описание</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($activities as $activity)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $activity->created_at->format('d.m.Y H:i') }}
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $activity->user->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $color = match($activity->action) {
                                    'create' => 'bg-green-100 text-green-700',
                                    'update' => 'bg-blue-100 text-blue-700',
                                    'delete' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp

                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ ucfirst($activity->action) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $activity->model_type }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $activity->description }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            Записей пока нет
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
