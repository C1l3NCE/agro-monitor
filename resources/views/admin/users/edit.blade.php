@extends('layouts.admin')

@section('title', 'Редактирование пользователя')
@section('header', 'Редактирование пользователя')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="bg-white/90 backdrop-blur-md
                shadow-xl rounded-3xl
                p-10 border border-gray-200">

        <form method="POST"
              action="{{ route('admin.users.update', $user) }}"
              class="space-y-8">
            @csrf
            @method('PUT')

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

                {{-- Имя --}}
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Имя
                    </label>
                    <input name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Email
                    </label>
                    <input name="email"
                           type="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="w-full px-4 py-3 rounded-xl
                                  border border-gray-200
                                  focus:ring-2 focus:ring-green-400
                                  transition text-sm">
                </div>

            </div>


            {{-- Роль --}}
            <div>
                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                    Роль
                </label>

                <select name="role"
                        required
                        class="w-full px-4 py-3 rounded-xl
                               border border-gray-200
                               focus:ring-2 focus:ring-green-400
                               transition text-sm">

                    <option value="admin" @selected($user->role === 'admin')>
                        Администратор
                    </option>

                    <option value="manager" @selected($user->role === 'manager')>
                        Менеджер
                    </option>

                    <option value="agronom" @selected($user->role === 'agronom')>
                        Агроном
                    </option>

                </select>
            </div>


            {{-- Смена пароля --}}
            <div class="border-t border-gray-100 pt-8 space-y-6">

                <h3 class="text-lg font-semibold text-gray-800">
                    Смена пароля
                </h3>

                <div class="grid md:grid-cols-2 gap-8">

                    <div>
                        <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                            Новый пароль
                        </label>
                        <input name="password"
                               type="password"
                               class="w-full px-4 py-3 rounded-xl
                                      border border-gray-200
                                      focus:ring-2 focus:ring-green-400
                                      transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wide text-gray-500 mb-2">
                            Подтвердить пароль
                        </label>
                        <input name="password_confirmation"
                               type="password"
                               class="w-full px-4 py-3 rounded-xl
                                      border border-gray-200
                                      focus:ring-2 focus:ring-green-400
                                      transition text-sm">
                    </div>

                </div>

                <p class="text-xs text-gray-500">
                    Оставьте поля пустыми, если не хотите менять пароль.
                </p>

            </div>


            {{-- Кнопки --}}
            <div class="flex justify-between items-center pt-8 border-t border-gray-100">

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
