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
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->text('konten');
            $table->enum('tipe', ['info', 'warning', 'danger', 'success'])->default('info');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // Pengumuman otomatis hilang jika expired
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('announcement_role', function (Blueprint $table) {
            $table->foreignUuid('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
