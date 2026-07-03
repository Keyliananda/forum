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
        Schema::create('spaces', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('visibility')->default('public')->index();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('topics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('topics')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('path');
            $table->unsignedSmallInteger('depth')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['space_id', 'path']);
            $table->unique(['space_id', 'parent_id', 'slug']);
            $table->index(['space_id', 'parent_id']);
            $table->index(['space_id', 'depth']);
        });

        Schema::create('discussions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->restrictOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('core_question');
            $table->text('body')->nullable();
            $table->string('status')->default('open');
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('last_replied_at')->nullable();
            $table->timestamps();

            $table->unique('slug');
            $table->index(['space_id', 'status', 'updated_at']);
            $table->index(['topic_id', 'status', 'last_replied_at']);
        });

        Schema::create('discussion_replies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('discussion_replies')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->string('status')->default('visible');
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();

            $table->index(['discussion_id', 'created_at']);
            $table->index(['discussion_id', 'parent_id']);
            $table->index(['author_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
        Schema::dropIfExists('discussions');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('spaces');
    }
};
