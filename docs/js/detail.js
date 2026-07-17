function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

async function renderDetail() {
    applyDocumentLocale();
    document.getElementById('header-judul').textContent = `🗺️ ${t('judul_situs')}`;
    document.getElementById('header-sub').textContent = t('sub_judul');
    renderLanguageSwitcher('lang-switcher-root');

    const params = new URLSearchParams(window.location.search);
    const slug = params.get('slug');
    const root = document.getElementById('detail-root');
    const locale = getCurrentLocale();

    if (!slug) {
        root.innerHTML = `<p>${t('tidak_ditemukan')}</p>`;
        return;
    }

    const res = await fetch(dataFileForLocale(locale));
    const geojson = await res.json();
    const features = geojson.features;
    const feature = features.find((f) => f.properties.slug === slug);

    if (!feature) {
        root.innerHTML = `<p>${t('tidak_ditemukan')}</p>`;
        return;
    }

    const p = feature.properties;

    const fotoHtml = p.foto
        ? `<img class="detail-photo" src="${escapeHtml(p.foto)}" alt="${escapeHtml(p.nama)}">`
        : '';

    const videoHtml = p.video_embed_url
        ? `<div class="video-wrap"><iframe src="${escapeHtml(p.video_embed_url)}" loading="lazy" allowfullscreen title="${escapeHtml(p.nama)}"></iframe></div>`
        : '';

    const ceritaHtml = p.cerita_lokal
        ? `<div class="cerita-box"><h2>${t('cerita_lokal')}</h2><p>${escapeHtml(p.cerita_lokal)}</p></div>`
        : '';

    const lainnya = features
        .filter((f) => f.properties.slug !== slug)
        .slice(0, 4);

    const lainnyaHtml = lainnya.length > 0 ? `
        <div class="lainnya">
            <h2>${t('titik_lainnya')}</h2>
            <div class="lainnya-grid">
                ${lainnya.map((f) => `
                    <a class="lainnya-card" href="detail.html?slug=${encodeURIComponent(f.properties.slug)}&lang=${locale}">
                        <strong>${escapeHtml(f.properties.nama)}</strong>
                        <span>${t('dusun')} ${escapeHtml(f.properties.dusun)}</span>
                    </a>
                `).join('')}
            </div>
        </div>
    ` : '';

    document.title = `${p.nama} — ${t('judul_situs')}`;
    document.getElementById('page-title').textContent = document.title;

    root.innerHTML = `
        <a class="back-link" href="index.html?lang=${locale}">&larr; ${t('kembali_ke_peta')}</a>
        ${fotoHtml}
        <div style="margin-top:1rem">
            <span class="badge">${escapeHtml(p.kategori_label)}</span>
            <span class="dusun-label">${t('dusun')} ${escapeHtml(p.dusun)}</span>
        </div>
        <h1 class="detail-title">${escapeHtml(p.nama)}</h1>
        ${p.deskripsi ? `<p>${escapeHtml(p.deskripsi)}</p>` : ''}
        ${videoHtml}
        ${ceritaHtml}
        <div class="coords">
            ${t('koordinat')}: ${p.latitude}, ${p.longitude}
            &middot;
            <a href="https://www.openstreetmap.org/?mlat=${p.latitude}&mlon=${p.longitude}#map=17/${p.latitude}/${p.longitude}" target="_blank" rel="noopener">${t('buka_di_peta')}</a>
        </div>
        ${lainnyaHtml}
    `;
}

document.addEventListener('DOMContentLoaded', renderDetail);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js').catch((err) => {
            console.warn('Gagal mendaftarkan service worker:', err);
        });
    });
}
