@extends('layouts.admin')

@section('header', 'Назначение полей: ' . $user->name)

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="bg-white/90 backdrop-blur-md
                rounded-3xl shadow-lg
                border border-gray-200
                p-8">

        <form method="POST"
              action="{{ route('admin.users.fields.update', $user) }}"
              class="space-y-8">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    Назначение полей
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Свободные поля можно назначить. Уже занятые — заблокированы.
                </p>
            </div>

            <!-- Список полей -->
            <div class="grid md:grid-cols-2 gap-4">

                @foreach($fields as $field)

                    @php
                        $assignedToCurrent = $field->user_id === $user->id;
                        $assignedToOther = $field->user_id && $field->user_id !== $user->id;
                    @endphp

                    <label class="flex items-center justify-between
                                  p-5 rounded-2xl border
                                  transition
                                  {{ $assignedToCurrent ? 'border-green-400 bg-green-50' : '' }}
                                  {{ $assignedToOther ? 'border-red-200 bg-red-50 opacity-80 cursor-not-allowed' : 'border-gray-200 hover:border-green-400 hover:bg-green-50 cursor-pointer' }}">

                        <div class="flex items-center gap-4">

                            <input type="checkbox"
                                   name="fields[]"
                                   value="{{ $field->id }}"
                                   {{ $assignedToCurrent ? 'checked' : '' }}
                                   {{ $assignedToOther ? 'disabled' : '' }}
                                   class="w-4 h-4 text-green-600
                                          border-gray-300 rounded
                                          focus:ring-green-400">

                            <div>
                                <div class="font-medium text-gray-800">
                                    {{ $field->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $field->crop }} · {{ $field->area }} га
                                </div>

                                @if($assignedToOther)
                                    <div class="text-xs text-red-600 mt-1 font-medium">
                                        Занято: {{ $field->user->name ?? 'Другой пользователь' }}
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Статус справа -->
                        <div class="text-xs font-medium">

                            @if($assignedToCurrent)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full">
                                    Назначено
                                </span>
                            @elseif($assignedToOther)
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full">
                                    Занято
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full">
                                    Свободно
                                </span>
                            @endif

                        </div>

                    </label>

                @endforeach

            </div>


            <!-- Кнопки -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-100">

                <a href="{{ route('admin.users.index') }}"
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
                    Сохранить изменения
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
