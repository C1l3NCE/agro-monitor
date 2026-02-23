<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Agro Monitor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden font-sans">

    <!-- Фон -->
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('images/landing.jpg') }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/70"></div>
    </div>

    <!-- Контент -->
    <div class="flex items-center justify-center h-full px-6">

        <!-- Glass карточка -->
        <div class="max-w-3xl w-full p-12 rounded-3xl
                    bg-white/10 backdrop-blur-xl
                    border border-white/20
                    shadow-2xl text-white text-center">

            <h1 class="text-5xl md:text-6xl font-bold mb-6 tracking-wide">
                Agro Monitor
            </h1>

            <p class="text-lg md:text-xl text-gray-200 mb-8 leading-relaxed">
                Интеллектуальная система мониторинга сельскохозяйственных полей.
                Анализ NDVI, выявление вредителей и агроаналитика
                на основе искусственного интеллекта.
            </p>

            <div class="mb-10 text-sm text-gray-300">
                © {{ date('Y') }} Авторы проекта:<br>
                Тарабанов А.В • Тулегенов Е.Н • Кусаинова Д.Б
            </div>

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center
          px-10 py-4 rounded-xl
          bg-gradient-to-r from-green-500 to-green-600
          hover:from-green-600 hover:to-green-700
          shadow-lg hover:shadow-green-500/40
          text-white font-semibold tracking-wide
          transition duration-300">
                Войти в систему
            </a>

        </div>

    </div>

</body>

</html>