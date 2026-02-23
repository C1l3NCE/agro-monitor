@extends('layouts.admin')

@section('title', 'Карта полей')
@section('header', 'Карта сельскохозяйственных полей')

@section('content')

<div class="space-y-10">

    <!-- Заголовок -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800">
            Карта полей
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Просмотр границ, расположения и состояния полей (NDVI)
        </p>
    </div>

    <!-- Карточка карты -->
    <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">

        <!-- Верхняя панель -->
        <div class="flex flex-wrap items-center justify-between
                    px-8 py-5
                    bg-white/80 backdrop-blur-md
                    border-b border-gray-100">

            <div class="text-sm text-gray-600">
                Всего полей:
                <span class="font-semibold text-gray-900 ml-1">
                    {{ $fields->count() }}
                </span>
            </div>

            <!-- Легенда -->
            <div class="flex items-center gap-6 text-xs text-gray-600">

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span>NDVI низкий</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span>NDVI средний</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span>NDVI высокий</span>
                </div>

            </div>

        </div>

        <!-- Карта -->
        <div id="map" class="w-full h-[650px]"></div>

    </div>

</div>


<script>
    const map = L.map('map', {
        zoomControl: true
    }).setView([53.2144, 63.6246], 7);

    // Базовые слои
    const osm = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        { attribution: '© OpenStreetMap contributors' }
    );

    const satellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/' +
        'World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { attribution: '© Esri, Maxar' }
    );

    osm.addTo(map);

    L.control.layers({
        "Карта": osm,
        "Спутник": satellite
    }, {}, {
        position: 'topright'
    }).addTo(map);

    const fields = @json($fields);
    let fitted = false;

    fields.forEach(field => {

        // Маркер
        L.marker([field.latitude, field.longitude])
            .addTo(map)
            .bindPopup(`
                <div style="font-size: 14px;">
                    <div style="font-weight:600;">${field.name}</div>
                    <div style="color:#6b7280;">${field.crop ?? ''}</div>
                    ${field.ndvi_avg !== null
                        ? `<div style="margin-top:4px;font-size:12px;">
                             NDVI: <strong>${field.ndvi_avg}</strong>
                           </div>`
                        : ''}
                </div>
            `);

        let geometry = null;

        if (field.geometry) {
            try {
                geometry = typeof field.geometry === 'string'
                    ? JSON.parse(field.geometry)
                    : field.geometry;
            } catch (e) {
                console.error('Ошибка парсинга geometry:', e);
            }
        }

        if (!geometry) return;

        // Цвет по NDVI
        let color = '#22c55e'; // зелёный по умолчанию

        if (field.ndvi_avg !== null) {
            if (field.ndvi_avg < 0.3) {
                color = '#ef4444'; // красный
            } else if (field.ndvi_avg < 0.6) {
                color = '#f59e0b'; // оранжевый
            }
        }

        const layer = L.geoJSON(geometry, {
            style: {
                color: color,
                weight: 2,
                fillColor: color,
                fillOpacity: 0.25
            }
        }).addTo(map);

        if (!fitted) {
            map.fitBounds(layer.getBounds());
            fitted = true;
        }
    });
</script>

@endsection
