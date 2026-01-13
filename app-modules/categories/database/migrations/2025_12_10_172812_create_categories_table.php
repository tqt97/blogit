<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->comment('parent id category');
            $table->string('name')->comment('category name');
            $table->string('slug')->nullable()->comment('slug for seo');
            $table->text('description')->nullable()->comment('description for category');
            $table->boolean('is_active')->default(true)->comment('show or hide category');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
