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
            
            // Slug digunakan untuk nama folder di Google Drive
            // Kita beri index agar pencarian folder saat upload sangat cepat
            $table->string('slug')->unique()->index(); 
            
            // Tambahkan Soft Deletes agar konsisten dengan sistem arsip kita
            $table->softDeletes(); 
            $table->timestamps();
        });

        Schema::create('file_category_role', function (Blueprint $table) {
            // Relasi ke file_categories
            $table->foreignId('file_category_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Relasi ke roles (Spatie). 
            // Kita sebutkan 'roles' secara eksplisit untuk berjaga-jaga
            $table->foreignId('role_id')
                ->constrained('roles') 
                ->cascadeOnDelete();

            // Indeks unik untuk mencegah role ganda di satu kategori
            $table->unique(['file_category_id', 'role_id'], 'unique_cat_role');
            
            // Optimasi: Indeks kebalikan untuk query: "Kategori apa saja yang bisa diakses role X?"
            $table->index('role_id');
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
