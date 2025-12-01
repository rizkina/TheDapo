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
            $table->uuid('id')->primary();
            $table->uuid('sekolah_id')->nullable();
            $table->string('ptk_terdaftar_id')->nullable();
            $table->string('ptk_induk')->nullable();
            $table->date('tanggal_surat_tugas')->nullable();
            $table->string('nama');
            $table->char('jenis_kelamin', 1)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('agama_id')->nullable();
            $table->string('agama_id_str')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nik')->nullable();
            $table->integer('jenis_ptk_id')->nullable();
            $table->string('jenis_ptk_id_str')->nullable();
            $table->integer('jabatan_ptk_id')->nullable();
            $table->string('jabatan_ptk_id_str')->nullable();
            $table->integer('status_kepegawaian_id')->nullable();
            $table->string('status_kepegawaian_id_str')->nullable();
            $table->string('nip')->nullable();
            $table->integer('pendidikan_terakhir')->nullable(); // kode dari pendidikans
            $table->string('bidang_studi_terakhir')->nullable();
            $table->string('pangkat_golongan_terakhir')->nullable();
            $table->json('riwayat_pendidikan')->nullable();
            $table->json('riwayat_kepangkatan')->nullable();

            $table->timestamps();

            $table->foreign('sekolah_id')->references('id')->on('sekolahs')->onDelete('set null');
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
