<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TitikWisata extends Model
{
    use HasFactory;

    public const KATEGORI = [
        'air_terjun' => 'Air Terjun',
        'pemandian' => 'Pemandian',
        'jalur_tracking' => 'Jalur Tracking',
        'kuliner' => 'Kuliner',
        'homestay' => 'Homestay',
        'budaya' => 'Budaya',
    ];

    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'dusun',
        'deskripsi',
        'cerita_lokal',
        'latitude',
        'longitude',
        'foto',
        'video_youtube_url',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (TitikWisata $titikWisata) {
            if (empty($titikWisata->slug)) {
                $titikWisata->slug = Str::slug($titikWisata->nama);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
