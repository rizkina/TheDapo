<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembelajarans', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menampung pembelajaran_id dari Dapodik
            $table->foreignUuid('rombel_id')->constrained('rombels')->onDelete('cascade');
            $table->foreignUuid('ptk_id')->nullable()->constrained('ptks')->nullOnDelete();
            
            $table->integer('mata_pelajaran_id');
            $table->string('mata_pelajaran_id_str');
            $table->string('ptk_terdaftar_id')->nullable();
            $table->string('nama_mata_pelajaran')->index();
            
            // Untuk pengelompokan mata pelajaran (Mapel Induk)
            $table->string('induk_pembelajaran_id')->nullable()->index();
            
            $table->integer('jam_mengajar_per_minggu')->nullable();
            $table->integer('status_di_kurikulum')->nullable();
            $table->string('status_di_kurikulum_str')->nullable();
            $table->softDeletes();
            $table->timestamps();

        });
        // --- OPTIMASI INDEKS (STRATEGI AHLI) ---

        // 1. Partial Index untuk pencarian berdasarkan GURU (Sangat Cepat untuk jadwal mengajar guru)
        // PostgreSQL akan menggunakan ini untuk query: WHERE ptk_id = ? AND deleted_at IS NULL
        DB::statement('CREATE INDEX idx_pembelajaran_ptk_active ON pembelajarans (ptk_id) WHERE deleted_at IS NULL');

        // 2. Partial Index untuk pencarian berdasarkan ROMBEL (Sangat Cepat untuk daftar mapel di kelas)
        // PostgreSQL akan menggunakan ini untuk query: WHERE rombel_id = ? AND deleted_at IS NULL
        DB::statement('CREATE INDEX idx_pembelajaran_rombel_active ON pembelajarans (rombel_id) WHERE deleted_at IS NULL');

        // 3. Indeks Gabungan Mata Pelajaran (Hanya yang aktif)
        DB::statement('CREATE INDEX idx_mapel_active ON pembelajarans (mata_pelajaran_id) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelajarans');
    }
};
