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
        Schema::create('siswas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->string('registrasi_id')->nullable()->index();
            $table->integer('jenis_pendaftaran_id')->nullable();
            $table->string('jenis_pendaftaran_id_str')->nullable();
            $table->string('nipd')->nullable()->index();
            $table->date('tanggal_masuk_sekolah')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->string('nama')->index();
            $table->string('nisn', 10)->nullable()->unique();
            $table->char('jenis_kelamin', 1)->nullable();
            $table->string('nik', 16)->nullable()->index();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedInteger('agama_id')->nullable(); // kode dari agamas
            $table->string('agama_id_str')->nullable();
            $table->string('nomor_telepon_rumah')->nullable();
            $table->string('nomor_telepon_seluler')->nullable();
            
            $table->string('nama_ayah')->nullable();
            $table->unsignedInteger('pekerjaan_ayah_id')->nullable(); // kode dari pekerjaans
            $table->string('pekerjaan_ayah_id_str')->nullable();
            
            $table->string('nama_ibu')->nullable();
            $table->unsignedInteger('pekerjaan_ibu_id')->nullable(); // kode dari pekerjaans
            $table->string('pekerjaan_ibu_id_str')->nullable();
            
            $table->string('nama_wali')->nullable();
            $table->unsignedInteger('pekerjaan_wali_id')->nullable(); // kode dari pekerjaans
            $table->string('pekerjaan_wali_id_str')->nullable();
            
            $table->integer('anak_keberapa')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            $table->string('email')->nullable()->index();
            
            $table->string('semester_id')->nullable();
            $table->uuid('anggota_rombel_id')->nullable();
            $table->uuid('rombongan_belajar_id')->nullable();
            $table->integer('tingkat_pendidikan_id')->nullable();
            $table->string('nama_rombel')->nullable();
            $table->integer('kurikulum_id')->nullable();
            $table->string('kurikulum_id_str')->nullable();
            $table->string('kebutuhan_khusus')->nullable();

            $table->timestamps();

            $table->foreign('agama_id')->references('kode')->on('agamas')->onDelete('set null');
            $table->foreign('pekerjaan_ayah_id')->references('kode')->on('pekerjaans')->onDelete('set null');
            $table->foreign('pekerjaan_ibu_id')->references('kode')->on('pekerjaans')->onDelete('set null');
            $table->foreign('pekerjaan_wali_id')->references('kode')->on('pekerjaans')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
