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
        Schema::create('google_drive_confs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Google Drive Utama');
            // Simpan Kredensial API di sini
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            // Hasil Token otomatis
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            
            $table->string('folder_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_drive_confs');
    }
};
