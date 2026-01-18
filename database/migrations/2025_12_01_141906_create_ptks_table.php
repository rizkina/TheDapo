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
            $table->foreignUuid('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->string('ptk_terdaftar_id')->nullable();
            $table->string('ptk_induk')->nullable();
            $table->date('tanggal_surat_tugas')->nullable();
            $table->string('nama');
            $table->char('jenis_kelamin', 1)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedInteger('agama_id')->nullable();
            $table->string('agama_id_str')->nullable();
            $table->string('nuptk')->nullable()->index();
            $table->string('nik')->nullable()->index();
            $table->integer('jenis_ptk_id')->nullable();
            $table->string('jenis_ptk_id_str')->nullable();
            $table->integer('jabatan_ptk_id')->nullable();
            $table->string('jabatan_ptk_id_str')->nullable();
            $table->integer('status_kepegawaian_id')->nullable();
            $table->string('status_kepegawaian_id_str')->nullable();
            $table->string('nip')->nullable()->index();
            $table->unsignedInteger('pendidikan_terakhir')->nullable(); // kode dari pendidikans
            $table->string('bidang_studi_terakhir')->nullable();
            $table->string('pangkat_golongan_terakhir')->nullable();
            $table->jsonb('riwayat_pendidikan')->nullable();
            $table->jsonb('riwayat_kepangkatan')->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->foreign('agama_id')->references('kode')->on('agamas');
            $table->foreign('pendidikan_terakhir')->references('kode')->on('pendidikans');

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
