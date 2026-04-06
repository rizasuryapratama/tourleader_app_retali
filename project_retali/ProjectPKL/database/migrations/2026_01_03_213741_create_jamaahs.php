<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaahs', function (Blueprint $table) {
            $table->id();

            // 🔗 Relasi ke absensi_jamaah
            $table->foreignId('absen_id')
                ->constrained('absensi_jamaah')
                ->cascadeOnDelete();

            // 🔗 Relasi ke tour leader
            $table->foreignId('assigned_tourleader_id')
                ->nullable()
                ->constrained('tour_leaders')
                ->nullOnDelete();

            // 🔑 URUTAN ABSEN DARI EXCEL
            $table->unsignedInteger('urutan_absen')->index();

            $table->string('nama_jamaah');
            $table->string('no_paspor')->nullable();
            $table->string('no_hp')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('kode_kloter')->nullable();
            $table->unsignedInteger('nomor_bus')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // 🛡️ 1 jamaah 1 urutan per sesi absen
            $table->unique([
                'absen_id',
                'assigned_tourleader_id',
                'urutan_absen'
            ]);

            // 🔥 TAMBAHAN INDEX UNTUK PERFORMA
            $table->index('assigned_tourleader_id');
            $table->index('absen_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaahs');
    }
};
