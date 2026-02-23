<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Вход — Agro Monitor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden font-sans">

    <!-- Фон -->
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('images/landing.jpg') }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/60 to-green-900/60"></div>
    </div>

    <!-- Контейнер -->
    <div class="flex items-center justify-center h-full px-4">

        <!-- Карточка -->
        <div class="w-full max-w-md p-10 rounded-2xl
                    bg-white/10 backdrop-blur-xl
                    border border-white/20
                    shadow-2xl text-white">

            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold tracking-wide">
                    Agro Monitor
                </h1>
                <p class="text-sm text-gray-300 mt-2">
                    Система мониторинга сельскохозяйственных полей
                </p>
            </div>

            @if(session('status'))
                <div class="mb-4 text-green-400 text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm mb-2 text-gray-300">
                        Email
                    </label>

                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl
                                  bg-white/20 border border-white/30
                                  placeholder-gray-300
                                  focus:outline-none focus:ring-2 focus:ring-green-400
                                  transition">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm mb-2 text-gray-300">
                        Пароль
                    </label>

                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl
                                  bg-white/20 border border-white/30
                                  placeholder-gray-300
                                  focus:outline-none focus:ring-2 focus:ring-green-400
                                  transition">
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm text-gray-300">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-white/40 bg-white/20">
                        <span>Запомнить меня</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="hover:text-green-400 transition">
                            Забыли пароль?
                        </a>
                    @endif
                </div>

                <!-- Кнопка -->
                <button type="submit" class="w-full py-3 rounded-xl
                               bg-gradient-to-r from-green-500 to-green-600
                               hover:from-green-600 hover:to-green-700
                               shadow-lg hover:shadow-green-500/40
                               font-semibold tracking-wide
                               transition duration-300">
                    Войти
                </button>

            </form>

            <div class="mt-8 text-center text-xs text-gray-400">
                © {{ date('Y') }} Agro Monitor
            </div>

        </div>

    </div>

</body>

</html>