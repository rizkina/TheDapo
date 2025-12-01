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
        Schema::create('anggota__rombels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rombel_id');
            $table->string('anggota_rombel_id');
            $table->uuid('peserta_didik_id');
            $table->integer('jenis_pendaftaran_id')->nullable();
            $table->string('jenis_pendaftaran_id_str')->nullable();

            $table->timestamps();

            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
            $table->foreign('peserta_didik_id')->references('id')->on('siswas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota__rombels');
    }
};
