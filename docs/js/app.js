// Koordinat Kantor Desa Sesaot, hasil decode Plus Code F65V+9PJ (presisi,
// bukan perkiraan) - dipakai sebagai titik tujuan tetap panel "Cara ke Sini".
const DESA_LATLNG = '-8.5415375,116.2442656';

function applyStaticTexts() {
    applyDocumentLocale();
    document.getElementById('page-title').textContent = t('judul_situs');
    document.getElementById('header-judul').textContent = `🗺️ ${t('judul_situs')}`;
    document.getElementById('header-sub').textContent = t('sub_judul');
    document.getElementById('arah-toggle-label').textContent = t('cara_ke_sini');
    renderLanguageSwitcher('lang-switcher-root');
}

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

    list.innerHTML = t('titik_asal').map((titik) => `
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
    'jalur/jalan-aspal-desa.geojson',
];

function initJalurLayer(map) {
    JALUR_FILES.forEach((url) => {
        fetch(url)
            .then((res) => res.json())
            .then((geojson) => {
                L.geoJSON(geojson, {
                    style: { color: '#c0392b', weight: 4, opacity: 0.8 },
                    onEachFeature: (feature, layer) => {
                        const p = feature.properties;
                        if (p?.nama) {
                            layer.bindPopup(`<strong>${p.nama}</strong>${p.deskripsi ? `<p>${p.deskripsi}</p>` : ''}`);
                        }
                    },
                }).addTo(map);
            })
            .catch((err) => console.warn('Gagal muat jalur:', url, err));
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

    initJalurLayer(map);

    const locale = getCurrentLocale();

    fetch(dataFileForLocale(locale))
        .then((res) => res.json())
        .then((geojson) => {
            const layer = L.geoJSON(geojson, {
                pointToLayer: (feature, latlng) => L.marker(latlng, { icon: markerIcon(feature.properties.kategori) }),
                onEachFeature: (feature, marker) => {
                    const p = feature.properties;
                    marker.bindPopup(`
                        <div class="popup-nama">${p.nama}</div>
                        <div class="popup-meta">${p.kategori_label} &middot; ${t('dusun')} ${p.dusun}</div>
                        <p>${p.deskripsi ?? ''}</p>
                        <a class="popup-link" href="detail.html?slug=${encodeURIComponent(p.slug)}&lang=${locale}">${t('lihat_detail')} &rarr;</a>
                        <br>
                        <a class="popup-link" href="${mapsDirectionToPointUrl(p.latitude, p.longitude)}" target="_blank" rel="noopener">🧭 ${t('petunjuk_arah')}</a>
                    `);
                },
            }).addTo(map);

            if (layer.getLayers().length > 0) {
                map.fitBounds(layer.getBounds(), { padding: [30, 30] });
            }
        });
}

document.addEventListener('DOMContentLoaded', applyStaticTexts);
document.addEventListener('DOMContentLoaded', initPeta);
document.addEventListener('DOMContentLoaded', initArahPanel);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js').catch((err) => {
            console.warn('Gagal mendaftarkan service worker:', err);
        });
    });
}
