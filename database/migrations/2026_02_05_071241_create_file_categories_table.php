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
        Schema::create('file_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

         Schema::create('file_category_role', function (Blueprint $table) {
            // Relasi ke file_categories
            $table->foreignId('file_category_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke roles (tabel bawaan Spatie)
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();

            // Index unik agar satu role tidak terdaftar ganda di kategori yang sama
            $table->unique(['file_category_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_categories');
    }
};
