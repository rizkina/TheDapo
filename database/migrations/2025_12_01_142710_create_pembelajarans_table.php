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
        Schema::create('pembelajarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rombel_id')->nullable()->constrained('rombels')->onDelete('cascade');
            $table->string('pembelajaran_id')->index();
            $table->integer('mata_pelajaran_id')->index();
            $table->string('mata_pelajaran_id_str');
            $table->string('ptk_terdaftar_id')->nullable();
            $table->foreignUuid('ptk_id')->nullable()->constrained('ptks')->nullOnDelete();
            $table->string('nama_mata_pelajaran')->index();
            $table->string('induk_pembelajaran_id')->nullable();
            $table->integer('jam_mengajar_per_minggu')->nullable();
            $table->integer('status_di_kurikulum')->nullable();
            $table->string('status_di_kurikulum_str')->nullable();

            $table->timestamps();

            // Optimasi Query: Sering dicari berdasarkan kombinasi rombel dan ptk
            $table->index(['rombel_id', 'ptk_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelajarans');
    }
};
