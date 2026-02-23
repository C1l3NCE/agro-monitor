<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Agro Monitor')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="flex">

        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-green-900 to-gray-900 text-white shadow-xl flex flex-col">

            <!-- Logo -->
            <div class="p-6 text-2xl font-bold border-b border-green-700">
                🌾 Agro Monitor
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2 text-sm">

                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-lg transition
               {{ request()->routeIs('dashboard') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                    📊 <span class="ml-2">Главная</span>
                </a>

                <a href="{{ route('fields.index') }}" class="flex items-center px-4 py-2 rounded-lg transition
               {{ request()->routeIs('fields.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                    🚜 <span class="ml-2">Поля</span>
                </a>

                <a href="{{ route('map.index') }}" class="flex items-center px-4 py-2 rounded-lg transition
               {{ request()->routeIs('map.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                    🗺️ <span class="ml-2">Карта</span>
                </a>

                <a href="{{ route('analytics.index') }}" class="flex items-center px-4 py-2 rounded-lg transition
               {{ request()->routeIs('analytics.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                    📈 <span class="ml-2">Аналитика</span>
                </a>

                @if(auth()->user()->hasRole(['admin', 'manager']))
                                <a href="{{ route('activity.index') }}" class="flex items-center px-4 py-2 rounded-lg transition
                       {{ request()->routeIs('activity.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                                    📋 <span class="ml-2">Журнал активности</span>
                                </a>
                @endif

                <a href="{{ route('chat.index') }}" class="flex items-center justify-between px-4 py-2 rounded-lg transition
   {{ request()->routeIs('chat.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">

                    <div class="flex items-center">
                        💬 <span class="ml-2">Сообщения</span>
                    </div>

                    <span id="chatBadge" class="hidden bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    </span>
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg transition
                                               {{ request()->routeIs('admin.users.*') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                        👤 <span class="ml-2">Пользователи</span>
                    </a>
                @endif

                @if(auth()->user()->hasRole(['agronom']))
                    <a href="{{ route('assistant.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg transition
                                               {{ request()->routeIs('assistant.index') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                        🤖 <span class="ml-2">ИИ помощник</span>
                    </a>
                @endif

                @if(auth()->user()->hasRole(['admin', 'manager', 'agronom']))
                    <a href="{{ route('assistant.history') }}"
                        class="flex items-center px-4 py-2 rounded-lg transition
                                               {{ request()->routeIs('assistant.history') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700' }}">
                        📚 <span class="ml-2">История анализов</span>
                    </a>
                @endif

            </nav>

            <!-- User block -->
            <div class="p-4 border-t border-green-700 bg-black/20">
                <div class="text-sm font-medium mb-2">
                    👤 {{ auth()->user()->name }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left text-red-400 hover:text-red-300 text-sm transition">
                        🚪 Выйти
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main content -->
        <main class="ml-64 w-full min-h-screen p-10">
            <h1 class="text-3xl font-bold mb-8">
                @yield('header')
            </h1>

            @yield('content')
        </main>

    </div>

    <script>
        function showGlobalNotification(text) {

            const notification = document.createElement('div');

            notification.className =
                "fixed bottom-6 right-6 bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg";

            notification.innerText = text;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        let lastUnreadCount = 0;

        function checkUnreadMessages() {

            fetch("{{ route('chat.unread.global') }}")
                .then(res => res.json())
                .then(data => {

                    const badge = document.getElementById('chatBadge');

                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                        badge.innerText = data.count;
                    } else {
                        badge.classList.add('hidden');
                    }

                    // Если количество увеличилось — показываем popup
                    if (data.count > lastUnreadCount) {
                        showGlobalNotification("Новое сообщение");
                    }

                    lastUnreadCount = data.count;
                });
        }

        setInterval(checkUnreadMessages, 3000);
        checkUnreadMessages();
    </script>

</body>

</html>