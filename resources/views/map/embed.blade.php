<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body style="margin:0">

<div id="map" style="height:100vh;"></div>

<script>
    const map = L.map('map').setView([53.2144, 63.6246], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
        .addTo(map);

    const fields = @json($fields);

    fields.forEach(field => {
        if (!field.geometry) return;

        let geometry = typeof field.geometry === 'string'
            ? JSON.parse(field.geometry)
            : field.geometry;

        L.geoJSON(geometry, {
            style: {
                color: '#16a34a',
                weight: 2,
                fillOpacity: 0.3
            }
        }).addTo(map);
    });
</script>

</body>
</html>
