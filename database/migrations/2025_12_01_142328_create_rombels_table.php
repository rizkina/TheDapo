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
        Schema::create('rombels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->string('nama')->index();
            $table->integer('tingkat_pendidikan_id')->nullable();
            $table->string('tingkat_pendidikan_id_str')->nullable();
            $table->string('semester_id')->nullable()->index();
            $table->integer('jenis_rombel')->nullable();
            $table->string('jenis_rombel_str')->nullable();
            $table->integer('kurikulum_id')->nullable();
            $table->string('kurikulum_id_str')->nullable();
            $table->string('id_ruang')->nullable();
            $table->string('id_ruang_str')->nullable();
            $table->enum('moving_class', ['Ya', 'Tidak'])->default('Tidak');
            $table->foreignUuid('ptk_id')->nullable()->constrained('ptks')->nullOnDelete();
            $table->string('ptk_id_str')->nullable();
            $table->string('jurusan_id')->nullable();
            $table->string('jurusan_id_str')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombels');
    }
};
