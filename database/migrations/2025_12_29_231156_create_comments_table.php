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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->unsignedInteger('likes_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('comments')->nullOnDelete();
            $table->index(['post_id', 'created_at', 'id'], 'comments_post_created_id_idx');
            $table->index(['parent_id', 'id'], 'comments_parent_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
