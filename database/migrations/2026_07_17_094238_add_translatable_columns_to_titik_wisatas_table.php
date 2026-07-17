<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->json('nama_i18n')->nullable()->after('nama');
            $table->json('deskripsi_i18n')->nullable()->after('deskripsi');
            $table->json('cerita_lokal_i18n')->nullable()->after('cerita_lokal');
        });

        DB::table('titik_wisatas')->orderBy('id')->each(function ($row) {
            DB::table('titik_wisatas')->where('id', $row->id)->update([
                'nama_i18n' => json_encode(['id' => $row->nama], JSON_UNESCAPED_UNICODE),
                'deskripsi_i18n' => $row->deskripsi !== null
                    ? json_encode(['id' => $row->deskripsi], JSON_UNESCAPED_UNICODE)
                    : null,
                'cerita_lokal_i18n' => $row->cerita_lokal !== null
                    ? json_encode(['id' => $row->cerita_lokal], JSON_UNESCAPED_UNICODE)
                    : null,
            ]);
        });

        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->dropColumn(['nama', 'deskripsi', 'cerita_lokal']);
        });

        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->renameColumn('nama_i18n', 'nama');
            $table->renameColumn('deskripsi_i18n', 'deskripsi');
            $table->renameColumn('cerita_lokal_i18n', 'cerita_lokal');
        });
    }

    public function down(): void
    {
        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->string('nama_text')->nullable()->after('nama');
            $table->text('deskripsi_text')->nullable()->after('deskripsi');
            $table->text('cerita_lokal_text')->nullable()->after('cerita_lokal');
        });

        DB::table('titik_wisatas')->orderBy('id')->each(function ($row) {
            $nama = json_decode($row->nama, true);
            $deskripsi = $row->deskripsi ? json_decode($row->deskripsi, true) : null;
            $ceritaLokal = $row->cerita_lokal ? json_decode($row->cerita_lokal, true) : null;

            DB::table('titik_wisatas')->where('id', $row->id)->update([
                'nama_text' => $nama['id'] ?? reset($nama),
                'deskripsi_text' => $deskripsi ? ($deskripsi['id'] ?? reset($deskripsi)) : null,
                'cerita_lokal_text' => $ceritaLokal ? ($ceritaLokal['id'] ?? reset($ceritaLokal)) : null,
            ]);
        });

        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->dropColumn(['nama', 'deskripsi', 'cerita_lokal']);
        });

        Schema::table('titik_wisatas', function (Blueprint $table) {
            $table->renameColumn('nama_text', 'nama');
            $table->renameColumn('deskripsi_text', 'deskripsi');
            $table->renameColumn('cerita_lokal_text', 'cerita_lokal');
        });
    }
};
