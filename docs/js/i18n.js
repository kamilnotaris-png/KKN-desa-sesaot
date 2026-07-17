const SUPPORTED_LOCALES = {
    id: { label: 'Indonesia', flag: '🇮🇩' },
    en: { label: 'English', flag: '🇬🇧' },
    ar: { label: 'العربية', flag: '🇸🇦' },
    zh: { label: '中文', flag: '🇨🇳' },
    ms: { label: 'Bahasa Melayu', flag: '🇲🇾' },
};

const DEFAULT_LOCALE = 'id';

const I18N_STRINGS = {
    id: {
        judul_situs: 'Wisata Desa Sesaot',
        sub_judul: 'Kec. Narmada, Lombok Barat',
        cara_ke_sini: 'Cara ke Sini',
        lihat_detail: 'Lihat detail',
        kembali_ke_peta: 'Kembali ke peta',
        dusun: 'Dusun',
        cerita_lokal: 'Cerita Lokal',
        titik_lainnya: 'Titik Wisata Lainnya',
        koordinat: 'Koordinat',
        buka_di_peta: 'buka di peta',
        tidak_ditemukan: 'Titik wisata tidak ditemukan.',
        titik_asal: [
            { nama: 'Bandara Internasional Lombok (LOP)', estimasi: '± 1 jam 15 menit' },
            { nama: 'Kota Mataram', estimasi: '± 45 menit' },
            { nama: 'Pelabuhan Lembar', estimasi: '± 1 jam' },
            { nama: 'Senggigi', estimasi: '± 50 menit' },
            { nama: 'Kawasan Mandalika', estimasi: '± 1 jam 30 menit' },
        ],
    },
    en: {
        judul_situs: 'Sesaot Village Tourism',
        sub_judul: 'Narmada, West Lombok',
        cara_ke_sini: 'Getting Here',
        lihat_detail: 'View details',
        kembali_ke_peta: 'Back to map',
        dusun: 'Hamlet',
        cerita_lokal: 'Local Story',
        titik_lainnya: 'Other Attractions',
        koordinat: 'Coordinates',
        buka_di_peta: 'open in map',
        tidak_ditemukan: 'Tourist spot not found.',
        titik_asal: [
            { nama: 'Lombok International Airport (LOP)', estimasi: '± 1h 15m' },
            { nama: 'Mataram City', estimasi: '± 45 min' },
            { nama: 'Lembar Port', estimasi: '± 1h' },
            { nama: 'Senggigi', estimasi: '± 50 min' },
            { nama: 'Mandalika Area', estimasi: '± 1h 30m' },
        ],
    },
    ar: {
        judul_situs: 'سياحة قرية سيساوت',
        sub_judul: 'نارمادا، لومبوك الغربية',
        cara_ke_sini: 'كيفية الوصول',
        lihat_detail: 'عرض التفاصيل',
        kembali_ke_peta: 'العودة إلى الخريطة',
        dusun: 'الحي',
        cerita_lokal: 'قصة محلية',
        titik_lainnya: 'معالم أخرى',
        koordinat: 'الإحداثيات',
        buka_di_peta: 'فتح في الخريطة',
        tidak_ditemukan: 'الموقع السياحي غير موجود.',
        titik_asal: [
            { nama: 'مطار لومبوك الدولي (LOP)', estimasi: '± ساعة و15 دقيقة' },
            { nama: 'مدينة ماتارام', estimasi: '± 45 دقيقة' },
            { nama: 'ميناء ليمبار', estimasi: '± ساعة واحدة' },
            { nama: 'سينغيغي', estimasi: '± 50 دقيقة' },
            { nama: 'منطقة ماندالیكا', estimasi: '± ساعة ونصف' },
        ],
    },
    zh: {
        judul_situs: '赛索特村旅游',
        sub_judul: '纳尔马达，西龙目',
        cara_ke_sini: '交通指南',
        lihat_detail: '查看详情',
        kembali_ke_peta: '返回地图',
        dusun: '村落',
        cerita_lokal: '当地故事',
        titik_lainnya: '其他景点',
        koordinat: '坐标',
        buka_di_peta: '在地图中打开',
        tidak_ditemukan: '未找到该景点。',
        titik_asal: [
            { nama: '龙目国际机场 (LOP)', estimasi: '约1小时15分钟' },
            { nama: '马塔兰市', estimasi: '约45分钟' },
            { nama: '伦巴港', estimasi: '约1小时' },
            { nama: '森吉吉', estimasi: '约50分钟' },
            { nama: '曼达利卡区', estimasi: '约1小时30分钟' },
        ],
    },
    ms: {
        judul_situs: 'Pelancongan Desa Sesaot',
        sub_judul: 'Narmada, Lombok Barat',
        cara_ke_sini: 'Cara ke Sini',
        lihat_detail: 'Lihat butiran',
        kembali_ke_peta: 'Kembali ke peta',
        dusun: 'Dusun',
        cerita_lokal: 'Cerita Tempatan',
        titik_lainnya: 'Tempat Menarik Lain',
        koordinat: 'Koordinat',
        buka_di_peta: 'buka dalam peta',
        tidak_ditemukan: 'Tempat pelancongan tidak dijumpai.',
        titik_asal: [
            { nama: 'Lapangan Terbang Antarabangsa Lombok (LOP)', estimasi: '± 1 jam 15 minit' },
            { nama: 'Bandar Mataram', estimasi: '± 45 minit' },
            { nama: 'Pelabuhan Lembar', estimasi: '± 1 jam' },
            { nama: 'Senggigi', estimasi: '± 50 minit' },
            { nama: 'Kawasan Mandalika', estimasi: '± 1 jam 30 minit' },
        ],
    },
};

function getCurrentLocale() {
    const params = new URLSearchParams(window.location.search);
    const fromQuery = params.get('lang');

    if (fromQuery && SUPPORTED_LOCALES[fromQuery]) {
        localStorage.setItem('locale', fromQuery);
        return fromQuery;
    }

    const stored = localStorage.getItem('locale');

    return stored && SUPPORTED_LOCALES[stored] ? stored : DEFAULT_LOCALE;
}

function t(key) {
    const locale = getCurrentLocale();

    return (I18N_STRINGS[locale] && I18N_STRINGS[locale][key]) ?? I18N_STRINGS[DEFAULT_LOCALE][key];
}

function dataFileForLocale(locale) {
    return locale === DEFAULT_LOCALE ? 'data.json' : `data.${locale}.json`;
}

function applyDocumentLocale() {
    const locale = getCurrentLocale();
    document.documentElement.setAttribute('lang', locale);
    document.documentElement.setAttribute('dir', locale === 'ar' ? 'rtl' : 'ltr');
}

function renderLanguageSwitcher(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const current = getCurrentLocale();

    const select = document.createElement('select');
    select.className = 'lang-switcher';
    select.setAttribute('aria-label', t('cara_ke_sini'));

    Object.entries(SUPPORTED_LOCALES).forEach(([code, info]) => {
        const option = document.createElement('option');
        option.value = code;
        option.textContent = `${info.flag} ${info.label}`;
        option.selected = code === current;
        select.appendChild(option);
    });

    select.addEventListener('change', () => {
        localStorage.setItem('locale', select.value);
        const url = new URL(window.location.href);
        url.searchParams.set('lang', select.value);
        window.location.href = url.toString();
    });

    el.appendChild(select);
}
