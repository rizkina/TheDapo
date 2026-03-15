<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ptks', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Diisi dari 'ptk_id' di JSON
            $table->foreignUuid('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->string('ptk_terdaftar_id')->nullable();
            $table->string('ptk_induk', 1)->nullable(); // 1 = Induk, 0 = Non Induk
            $table->date('tanggal_surat_tugas')->nullable();
            $table->string('nama')->index();
            $table->string('foto')->nullable();
            $table->char('jenis_kelamin', 1)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            $table->unsignedInteger('agama_id')->nullable();
            $table->string('agama_id_str')->nullable();
            
            $table->string('nuptk', 16)->nullable()->index();
            $table->string('nik', 16)->nullable()->index();
            $table->string('nip', 18)->nullable()->index();
            
            $table->integer('jenis_ptk_id')->nullable();
            $table->string('jenis_ptk_id_str')->nullable();
            $table->integer('jabatan_ptk_id')->nullable();
            $table->string('jabatan_ptk_id_str')->nullable();
            $table->integer('status_kepegawaian_id')->nullable();
            $table->string('status_kepegawaian_id_str')->nullable();
            
            // Perubahan: Gunakan string jika API mengirim "S1", "S2" 
            // atau tetap integer jika Anda punya tabel konversi
            $table->unsignedInteger('pendidikan_terakhir')->nullable(); 
            $table->string('bidang_studi_terakhir')->nullable();
            $table->string('pangkat_golongan_terakhir')->nullable();
            
            // PostgreSQL Jsonb untuk performa tinggi
            $table->jsonb('riwayat_pendidikan')->nullable();
            $table->jsonb('riwayat_kepangkatan')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('agama_id')->references('kode')->on('agamas')->onDelete('set null');
            $table->foreign('pendidikan_terakhir')->references('kode')->on('pendidikans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ptks');
    }
};
