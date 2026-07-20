<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('peta.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
@foreach ($titikWisata as $titik)
    <url>
        <loc>{{ route('titik-wisata.show', $titik) }}</loc>
        <lastmod>{{ $titik->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
