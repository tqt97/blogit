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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 190)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at', 'id'], 'posts_status_published_id_idx');
            $table->index(['category_id', 'published_at', 'id'], 'posts_category_published_id_idx');
            $table->index(['user_id', 'id'], 'posts_user_id_idx');
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            $table->primary(['post_id', 'tag_id'], 'post_tag_pk');
            $table->index(['tag_id', 'post_id'], 'post_tag_tag_post_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('posts');
    }
};
