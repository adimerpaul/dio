{{-- resources/views/casos/pdfHojaRuta.blade.php --}}
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ubicacion </title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        :root { --font: "DejaVu Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
        * { box-sizing: border-box }
        html, body { margin:0; padding:0; font-family:var(--font); color:#111 }
        .wrap { padding: 10px }
        .head {
            display:grid; grid-template-columns:1fr auto; gap:6px; align-items:center;
            border:1px solid #e5e7eb; padding:8px 10px; border-radius:8px; background:#f8fafc
        }
        .title { margin:0; font-size:14px; font-weight:700 }
        .meta { margin:0; font-size:11px; color:#475569 }
        .badge { font-size:11px; background:#eef3ff; border:1px solid #cfdcff; padding:3px 8px; border-radius:999px }
        .grid { display:grid; gap:6px; margin-top:8px; grid-template-columns:repeat(4, 1fr) }
        .cell { border:1px solid #e5e7eb; border-radius:8px; padding:6px 8px; background:#fff; min-height:36px }
        .lbl { font-size:10px; color:#6b7280; display:block; margin-bottom:1px }
        .val { font-size:12px; font-weight:600; color:#111; word-break:break-word }
        #map { width:100%; height:460px; margin-top:8px; border:1px solid #e5e7eb; border-radius:8px }
        @media print {
            @page { size:A4 portrait; margin:8mm 8mm }
            .wrap { padding:0 }
            #map { height:14cm !important }
            .head { background:#fff }
            .no-print { display:none !important }
        }
        .toolbar { margin-top:8px; display:flex; gap:6px }
        .btn { padding:6px 10px; border:1px solid #cfdcff; background:#eef3ff; border-radius:8px; font-size:11px; cursor:pointer }
        .btn:hover { background:#e5edff }
    </style>
</head>
<body>
<div class="wrap">

    <center>
        <div style="margin-top:8px;padding-top:8px;border-top:1px solid #e5e7eb;width:650px;">
            <div id="map"></div>
        </div>
    </center>

    <div class="toolbar no-print">
        <button class="btn" onclick="window.print()">Imprimir</button>
        <button class="btn" onclick="window.close()">Cerrar</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    (function () {
        // Datos inyectados desde el controller
        const LAT  = {{ json_encode($LAT) }};
        const LNG  = {{ json_encode($LNG) }};
        const HAS  = {{ $HAS ? 'true' : 'false' }};
        const who  = "Ubicacion"


        // Fix íconos Leaflet
        L.Icon.Default.mergeOptions({
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            iconUrl:       'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl:     'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'
        });

        const map = L.map('map').setView([LAT, LNG], HAS ? 17 : 13);
        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const center = L.latLng(LAT, LNG);
        map.setView(center, HAS ? 17 : 13);

        const marker = L.marker(center, { zIndexOffset: 1000, title: `Ubicación del ${who}` }).addTo(map);
        marker.bindPopup(
            `<b>Latitud:</b> ${LAT}<br>` +
            `<b>Longitud:</b> ${LNG}<br>`
        );

        // Círculo de respaldo, por si el PNG falla
        L.circleMarker(center, { radius:8, weight:2, fillOpacity:0.5 }).addTo(map);

        L.control.scale({ metric:true, imperial:false }).addTo(map);

        map.whenReady(() => {
            map.invalidateSize(true);
            marker.openPopup();
            map.setView(center, HAS ? 17 : 13, { animate:false });
        });

        let printed = false;
        function goPrint() {
            if (printed) return;
            printed = true;
            map.setView(center, HAS ? 17 : 13, { animate:false });
            setTimeout(() => window.print(), 250);
        }
        tiles.once('load', goPrint);
        setTimeout(goPrint, 5000); // respaldo

        window.addEventListener('afterprint', () => setTimeout(() => {
            try { window.close(); } catch (_) {}
        }, 250));
        window.addEventListener('focus', () => {
            if (printed) setTimeout(() => { try { window.close(); } catch (_) {} }, 200);
        });
    })();
</script>
</body>
</html>
