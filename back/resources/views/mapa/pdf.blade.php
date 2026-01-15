{{-- resources/views/mapa/pdf.blade.php --}}
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ubicación</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="anonymous"/>

    <style>
        :root { --font: "DejaVu Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
        * { box-sizing: border-box }
        html, body { margin:0; padding:0; font-family:var(--font); color:#111 }
        .wrap { padding: 10px }
        #map { width:100%; height:460px; margin:8px auto 0; border:1px solid #e5e7eb; border-radius:8px; max-width: 650px; }

        /* Botonera solo para vista navegador */
        .toolbar { margin-top:8px; display:flex; gap:6px }
        .btn { padding:6px 10px; border:1px solid #cfdcff; background:#eef3ff; border-radius:8px; font-size:11px; cursor:pointer }
        .btn:hover { background:#e5edff }

        /* Icono SVG inline para impresión confiable */
        .leaflet-div-icon.pin { background: transparent; border: 0; }
        .pin svg { display:block; width:30px; height:44px; }

        /* Tooltip permanente (visible en pantalla e impresión) */
        .leaflet-tooltip.pin-tip{
            background:#111; color:#fff; border:1px solid #00000022;
            padding:4px 6px; border-radius:6px; box-shadow:0 1px 2px rgba(0,0,0,.2);
            font-size:11px;
        }

        @media print {
            @page { size:A4 portrait; margin:8mm }
            .wrap { padding:0 }
            #map { height:14cm !important }
            .no-print { display:none !important }
            .pin svg, .leaflet-tooltip.pin-tip { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body style="padding: 20px">
<div style="text-align: right;font-size: 10px;font-weight: bold">{{ date('d/m/Y H:i') }}</div>
<div style="text-align: center;font-size: 40px;font-weight: bold; margin-top: 4px">
    {{(($label) ?? 'Ubicación')}}
</div>
<div class="wrap">
    <div id="map"></div>

    <div class="toolbar no-print">
        <button class="btn" onclick="window.print()">Imprimir</button>
        <button class="btn" onclick="window.close()">Cerrar</button>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="anonymous"></script>

<script>
    (function () {
        // Datos del servidor
        const LAT = {{ json_encode($LAT) }};
        const LNG = {{ json_encode($LNG) }};
        const HAS = {{ $HAS ? 'true' : 'false' }};  // ¿coords válidas?
        const PIN = {{ $PIN ? 'true' : 'false' }};  // ¿mostrar pin?

        const center = L.latLng(LAT, LNG);

        const map = L.map('map', { zoomControl: true })
            .setView(center, HAS ? 17 : 13);

        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Círculo de respaldo (si se oculta el pin, igual se ve el punto)
        const dot = L.circleMarker(center, { radius: 8, weight: 2, fillOpacity: 0.5 }).addTo(map);

        // Pin SVG inline + tooltip permanente
        if (PIN) {
            const pinIcon = L.divIcon({
                className: 'pin leaflet-div-icon',
                iconSize: [30, 44],
                iconAnchor: [15, 44],
                popupAnchor: [0, -36],
                html: `
        <svg viewBox="0 0 30 44" xmlns="http://www.w3.org/2000/svg" aria-label="Ubicación">
          <defs>
            <filter id="s" x="-20%" y="-20%" width="140%" height="140%">
              <feDropShadow dx="0" dy="1" stdDeviation="1" flood-opacity="0.25"/>
            </filter>
          </defs>
          <path filter="url(#s)"
                d="M15 0C6.72 0 0 6.72 0 15c0 10.5 13.5 27.75 14.08 28.5a1.2 1.2 0 0 0 1.84 0C16.5 42.75 30 25.5 30 15 30 6.72 23.28 0 15 0z"
                fill="#2563eb"/>
          <circle cx="15" cy="15" r="6.5" fill="#fff"/>
        </svg>`
            });

            const marker = L.marker(center, { icon: pinIcon, zIndexOffset: 1000, title: 'Ubicación' }).addTo(map);

            // Tooltip PERMANENTE (se imprime)
            marker.bindTooltip(
                `<div><b>Lat:</b> ${Number(LAT).toFixed(6)}<br><b>Lng:</b> ${Number(LNG).toFixed(6)}</div>`,
                { permanent: true, direction: 'top', offset: [0, -46], opacity: 1, className: 'pin-tip' }
            );

            // (Opcional) también puedes dejar un popup para clic
            marker.bindPopup(`<b>Latitud:</b> ${LAT}<br><b>Longitud:</b> ${LNG}`);
        }

        L.control.scale({ metric: true, imperial: false }).addTo(map);

        // Asegurar layout antes de imprimir
        map.whenReady(() => {
            map.invalidateSize(true);
            map.setView(center, HAS ? 17 : 13, { animate: false });
        });

        let printed = false;
        function goPrint() {
            if (printed) return;
            printed = true;
            map.setView(center, HAS ? 17 : 13, { animate: false });
            setTimeout(() => window.print(), 250);
        }
        tiles.once('load', goPrint);
        setTimeout(goPrint, 5000); // respaldo por si 'load' no dispara

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
