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
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('dapodik_users')->cascadeOnDelete();
            $table->foreignId('file_category_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('disk')->default('google');
            $table->softDeletes();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('dapodik_users')->onDelete('cascade');
            $table->foreign('file_category_id')->references('id')->on('file_categories')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
