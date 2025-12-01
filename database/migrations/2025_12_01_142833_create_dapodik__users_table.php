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
        Schema::create('dapodik__users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sekolah_id')->nullable();
            $table->string('username')->unique();
            $table->string('nama');
            $table->string('peran_id_str');
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->uuid('peserta_didik_id')->nullable();
            $table->string('password')->nullable();

            $table->timestamps();

            $table->foreign('sekolah_id')->references('id')->on('sekolahs')->onDelete('set null');
            $table->foreign('ptk_id')->references('id')->on('ptks')->onDelete('set null');
            $table->foreign('peserta_didik_id')->references('id')->on('siswas')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapodik__users');
    }
};
