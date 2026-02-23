@extends('layouts.admin')

@section('title', 'Контур поля')
@section('header', 'Контур: ' . $field->name)

@section('content')

<div id="map" style="height: 600px"
     class="rounded shadow"></div>

<button id="save"
        class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
    💾 Сохранить контур
</button>

<script>
    const map = L.map('map').setView(
        [{{ $field->latitude }}, {{ $field->longitude }}],
        15
    );

    const satellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/' +
        'World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            attribution: '© Esri'
        }
    ).addTo(map);

    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        draw: {
            polygon: true,
            polyline: false,
            rectangle: false,
            circle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems
        }
    });
    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, function (e) {
        drawnItems.clearLayers();
        drawnItems.addLayer(e.layer);
    });

    document.getElementById('save').onclick = function () {
    const data = drawnItems.toGeoJSON();

    if (!data.features.length) {
        alert('Нарисуйте контур поля');
        return;
    }

    const geometry = data.features[0].geometry;

    // 📐 ПЛОЩАДЬ В КВАДРАТНЫХ МЕТРАХ
    const areaM2 = turf.area(geometry);

    // 📐 ПЕРЕВОД В ГЕКТАРЫ
    const areaHa = (areaM2 / 10000).toFixed(2);

    fetch('{{ route('fields.geometry', $field) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            geometry: geometry,
            calculated_area: areaHa
        })
    }).then(() => {
        alert(`Контур сохранён\nПлощадь: ${areaHa} га`);
        window.location.href = '{{ route('fields.index') }}';
    });
};

</script>

@endsection
