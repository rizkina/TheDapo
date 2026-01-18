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
            $table->foreignUuid('rombel_id')->nullable()->constrained('rombels')->onDelete('cascade');

            $table->string('anggota_rombel_id')->nullable()->index();

            $table->foreignUuid('peserta_didik_id')->nullable()->constrained('siswas')->onDelete('cascade');
            $table->integer('jenis_pendaftaran_id')->nullable();
            $table->string('jenis_pendaftaran_id_str')->nullable();
            $table->softDeletes();

            $table->timestamps();

            // Mencegah duplikasi siswa di rombel yang sama
            $table->unique(['rombel_id', 'peserta_didik_id'], 'unique_siswa_rombel');

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
