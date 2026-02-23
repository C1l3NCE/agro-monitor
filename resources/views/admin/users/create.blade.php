@extends('layouts.admin')

@section('header', 'Создание пользователя')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- Заголовок -->
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800">
            Новый пользователь
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Создание учетной записи и назначение роли доступа
        </p>
    </div>

    <!-- Карточка -->
    <div class="bg-white/90 backdrop-blur-md
                rounded-3xl shadow-xl
                border border-gray-200">

        <div class="p-10">

            {{-- Ошибки --}}
            @if($errors->any())
                <div class="mb-8 bg-red-50 border border-red-200
                            text-red-700 p-5 rounded-2xl">
                    <ul class="list-disc ml-6 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.users.store') }}"
                  class="space-y-8">
                @csrf

                <!-- Имя -->
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Имя пользователя
                    </label>
                    <input name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Введите имя"
                           class="w-full px-5 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Email
                    </label>
                    <input name="email"
                           type="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="example@mail.com"
                           class="w-full px-5 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                <!-- Пароль -->
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Пароль
                    </label>
                    <input name="password"
                           type="password"
                           required
                           placeholder="Минимум 6 символов"
                           class="w-full px-5 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                <!-- Роль -->
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Роль
                    </label>
                    <select name="role"
                            class="w-full px-5 py-3 rounded-xl
                                   border border-gray-200
                                   focus:ring-2 focus:ring-green-400
                                   transition text-sm">

                        <option value="agronom">Агроном</option>
                        <option value="manager">Менеджер</option>
                        <option value="admin">Администратор</option>

                    </select>
                </div>

                <!-- Кнопки -->
                <div class="pt-8 flex justify-between items-center border-t border-gray-100">

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
                        Создать пользователя
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
