import L from 'leaflet';

// Koordinat Kantor Desa Sesaot, hasil decode Plus Code F65V+9PJ (presisi,
// bukan perkiraan) - dipakai sebagai titik tujuan tetap panel "Cara ke Sini".
const DESA_LATLNG = '-8.5415375,116.2442656';

const I18N = window.PETA_I18N ?? {};
const TITIK_ASAL = I18N.titikAsal ?? [];

function mapsDirectionUrl(namaAsal) {
    const params = new URLSearchParams({
        api: '1',
        origin: namaAsal,
        destination: DESA_LATLNG,
        travelmode: 'driving',
    });
    return `https://www.google.com/maps/dir/?${params.toString()}`;
}

function mapsDirectionToPointUrl(lat, lng) {
    const params = new URLSearchParams({ api: '1', destination: `${lat},${lng}` });
    return `https://www.google.com/maps/dir/?${params.toString()}`;
}

function initArahPanel() {
    const toggle = document.getElementById('arah-toggle');
    const list = document.getElementById('arah-list');
    if (!toggle || !list) return;

    list.innerHTML = TITIK_ASAL.map((titik) => `
        <a href="${mapsDirectionUrl(titik.nama)}" target="_blank" rel="noopener" class="arah-item">
            <span class="arah-item-nama">${titik.nama}</span>
            <span class="arah-item-estimasi">${titik.estimasi}</span>
        </a>
    `).join('');

    toggle.addEventListener('click', () => list.classList.toggle('hidden'));
}

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

// Jalur hasil survei GPS lapangan (GeoJSON), ditampilkan sebagai lapisan
// sendiri di atas tile OSM - supaya tidak perlu menunggu tile OSM di-render
// ulang tiap kali ada jalur baru disurvei. Tambahkan file baru ke daftar ini
// begitu ada hasil survei jalur berikutnya (lihat survei-gps/README.md).
const JALUR_FILES = [
    '/jalur/jalan-aspal-desa.geojson',
];

async function loadJalurLayers(map) {
    const layers = await Promise.all(JALUR_FILES.map(async (url) => {
        try {
            const response = await fetch(url, { cache: 'no-cache' });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const geojson = await response.json();

            return L.geoJSON(geojson, {
                style: { color: '#c0392b', weight: 4, opacity: 0.8 },
                onEachFeature: (feature, layer) => {
                    const p = feature.properties;
                    if (p?.nama) {
                        layer.bindPopup(`<strong>${p.nama}</strong>${p.deskripsi ? `<p class="mt-1">${p.deskripsi}</p>` : ''}`);
                    }
                },
            }).addTo(map);
        } catch (err) {
            console.warn('Gagal muat jalur:', url, err);
            return null;
        }
    }));

    return layers.filter(Boolean);
}

async function loadTitikWisataLayer(map) {
    const response = await fetch('/api/titik-wisata', { cache: 'no-cache' });
    if (!response.ok) {
        throw new Error(`Gagal memuat titik wisata: HTTP ${response.status}`);
    }

    const geojson = await response.json();

    return L.geoJSON(geojson, {
        pointToLayer: (feature, latlng) => L.marker(latlng, { icon: markerIcon(feature.properties.kategori) }),
        onEachFeature: (feature, marker) => {
            const p = feature.properties;
            marker.bindPopup(`
                <div class="text-sm">
                    <strong>${p.nama}</strong><br>
                    <span class="text-gray-500">${p.kategori_label} &middot; ${I18N.dusun ?? 'Dusun'} ${p.dusun}</span>
                    <p class="mt-1">${p.deskripsi ?? ''}</p>
                    <a href="${p.detail_url}" class="text-wisata-green-600 font-semibold">${I18N.lihatDetail ?? 'Lihat detail'} &rarr;</a>
                    <br>
                    <a href="${mapsDirectionToPointUrl(p.latitude, p.longitude)}" target="_blank" rel="noopener" class="text-wisata-green-600 font-semibold">🧭 ${I18N.petunjukArah ?? 'Petunjuk Arah'}</a>
                </div>
            `);
        },
    }).addTo(map);
}

function fitMapToLayers(map, layers) {
    const bounds = L.latLngBounds([]);

    layers.filter(Boolean).forEach((layer) => {
        if (typeof layer.getBounds !== 'function') return;

        const layerBounds = layer.getBounds();
        if (layerBounds.isValid()) {
            bounds.extend(layerBounds);
        }
    });

    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
}

async function initPeta() {
    const el = document.getElementById('peta-wisata');
    if (!el) return;

    const map = L.map(el).setView([-8.524, 116.264], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const [jalurLayers, titikLayer] = await Promise.all([
        loadJalurLayers(map),
        loadTitikWisataLayer(map).catch((err) => {
            console.warn(err);
            return null;
        }),
    ]);

    // Pastikan jalur hasil survei dan seluruh marker masuk dalam tampilan awal.
    fitMapToLayers(map, [...jalurLayers, titikLayer]);
}

document.addEventListener('DOMContentLoaded', initPeta);
document.addEventListener('DOMContentLoaded', initArahPanel);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.warn('Gagal mendaftarkan service worker:', err);
        });
    });
}
