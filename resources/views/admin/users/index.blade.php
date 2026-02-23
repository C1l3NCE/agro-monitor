@extends('layouts.admin')

@section('header', 'Пользователи')

@section('content')

<div class="flex items-center justify-between mb-10">

    <div>
        <h2 class="text-xl font-semibold text-gray-800">
            Пользователи системы
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Управление доступом и назначением полей
        </p>
    </div>

    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center justify-center
              px-6 py-3 rounded-xl
              bg-gradient-to-r from-green-500 to-green-600
              hover:from-green-600 hover:to-green-700
              text-white font-semibold
              shadow-md hover:shadow-green-500/30
              transition duration-300">
        Создать пользователя
    </a>

</div>


<div class="bg-white/90 backdrop-blur-md
            rounded-3xl shadow-lg
            border border-gray-200 overflow-hidden">

    <table class="w-full text-sm text-gray-700">

        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-6 py-5 text-left">Пользователь</th>
                <th class="px-6 py-5 text-left">Email</th>
                <th class="px-6 py-5 text-center">Роль</th>
                <th class="px-6 py-5 text-center">Поля</th>
                <th class="px-6 py-5 text-right">Действия</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">

        @forelse($users as $user)

            <tr class="hover:bg-gray-50 transition duration-200">

                <!-- Имя -->
                <td class="px-6 py-5 font-semibold text-gray-900">
                    {{ $user->name }}
                </td>

                <!-- Email -->
                <td class="px-6 py-5 text-gray-600">
                    {{ $user->email }}
                </td>

                <!-- Роль -->
                <td class="px-6 py-5 text-center">
                    @php
                        $roleColor = match ($user->role) {
                            'admin' => 'bg-red-50 text-red-600',
                            'manager' => 'bg-blue-50 text-blue-600',
                            'agronom' => 'bg-green-50 text-green-600',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp

                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleColor }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>

                <!-- Количество полей -->
                <td class="px-6 py-5 text-center">
                    <span class="inline-flex items-center justify-center
                                 bg-gray-100 text-gray-700
                                 px-3 py-1 rounded-full text-xs font-medium">
                        {{ $user->fields->count() }}
                    </span>
                </td>

                <!-- Действия -->
                <td class="px-6 py-5 text-right">

                    <div class="flex justify-end gap-6 text-sm font-medium">

                        <a href="{{ route('admin.users.fields.edit', $user) }}"
                           class="text-green-600 hover:text-green-800 transition">
                            Назначить
                        </a>

                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-blue-600 hover:text-blue-800 transition">
                            Редактировать
                        </a>

                        <form method="POST"
                              action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Удалить пользователя?')">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:text-red-800 transition">
                                Удалить
                            </button>
                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="5" class="px-6 py-14 text-center text-gray-400">
                    Пользователи не найдены
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection
