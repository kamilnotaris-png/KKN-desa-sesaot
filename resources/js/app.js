import L from 'leaflet';

const KATEGORI_ICON = {
    air_terjun: '💧',
    pemandian: '🏊',
    jalur_tracking: '🥾',
    kuliner: '🍢',
    homestay: '🏠',
    budaya: '🎭',
};

function markerIcon(kategori) {
    return L.divIcon({
        className: 'titik-wisata-marker',
        html: `<span>${KATEGORI_ICON[kategori] ?? '📍'}</span>`,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
    });
}

function initPeta() {
    const el = document.getElementById('peta-wisata');
    if (!el) return;

    const map = L.map(el).setView([-8.524, 116.264], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    fetch('/api/titik-wisata')
        .then((res) => res.json())
        .then((geojson) => {
            const layer = L.geoJSON(geojson, {
                pointToLayer: (feature, latlng) => L.marker(latlng, { icon: markerIcon(feature.properties.kategori) }),
                onEachFeature: (feature, marker) => {
                    const p = feature.properties;
                    marker.bindPopup(`
                        <div class="text-sm">
                            <strong>${p.nama}</strong><br>
                            <span class="text-gray-500">${p.kategori_label} &middot; Dusun ${p.dusun}</span>
                            <p class="mt-1">${p.deskripsi ?? ''}</p>
                            <a href="${p.detail_url}" class="text-wisata-green-600 font-semibold">Lihat detail &rarr;</a>
                        </div>
                    `);
                },
            }).addTo(map);

            if (layer.getLayers().length > 0) {
                map.fitBounds(layer.getBounds(), { padding: [30, 30] });
            }
        });
}

document.addEventListener('DOMContentLoaded', initPeta);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.warn('Gagal mendaftarkan service worker:', err);
        });
    });
}
