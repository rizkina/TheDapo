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
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke User & Kategori
            $table->foreignUuid('user_id')->constrained('dapodik_users')->cascadeOnDelete();
            $table->foreignId('file_category_id')->constrained('file_categories')->cascadeOnDelete();
            
            // Metadata File
            $table->string('file_path'); // Berisi Path/ID di Google Drive
            $table->string('file_name'); // Nama tampilan di aplikasi
            $table->string('original_name'); // Nama asli file saat diunggah
            $table->string('mime_type', 100); // Batasi length untuk efisiensi
            $table->unsignedBigInteger('size'); // Gunakan BigInteger untuk mendukung file > 2GB
            
            $table->string('disk')->default('google');
            
            $table->softDeletes();
            $table->timestamps();

        });
        /**
         * OPTIMASI POSTGRESQL (PARTIAL INDEX):
         * Karena kita menggunakan Soft Deletes, hampir 100% query akan menggunakan 'WHERE deleted_at IS NULL'.
         * Partial Index jauh lebih kecil dan cepat daripada Index biasa karena hanya mencatat file yang AKTIF.
         */
        DB::statement('CREATE INDEX idx_files_user_category_active ON files (user_id, file_category_id) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
