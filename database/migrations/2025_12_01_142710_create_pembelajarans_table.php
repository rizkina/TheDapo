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
            $table->uuid('rombel_id');
            $table->string('pembelajaran_id');
            $table->integer('mata_pelajaran_id');
            $table->string('mata_pelajaran_id_str');
            $table->string('ptk_terdaftar_id')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->string('nama_mata_pelajaran');
            $table->string('induk_pembelajaran_id')->nullable();
            $table->integer('jam_mengajar_per_minggu')->nullable();
            $table->integer('status_di_kurikulum')->nullable();
            $table->string('status_di_kurikulum_str')->nullable();

            $table->timestamps();

            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
            $table->foreign('ptk_id')->references('id')->on('ptks')->onDelete('set null');

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
