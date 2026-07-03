<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_positions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->string('status')->default('open');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['discussion_id', 'slug']);
            $table->index(['discussion_id', 'status', 'sort_order']);
        });

        Schema::create('discussion_claims', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('statement');
            $table->string('slug');
            $table->string('type')->default('factual');
            $table->string('status')->default('open');
            $table->timestamps();

            $table->unique(['discussion_id', 'slug']);
            $table->index(['discussion_id', 'type', 'status']);
        });

        Schema::create('discussion_position_claims', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('position_id')->constrained('discussion_positions')->cascadeOnDelete();
            $table->foreignId('claim_id')->constrained('discussion_claims')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['position_id', 'claim_id']);
        });

        Schema::create('discussion_arguments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained('discussion_claims')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('discussion_arguments')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->text('body');
            $table->string('status')->default('open');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['discussion_id', 'type', 'status']);
            $table->index(['claim_id', 'type', 'status']);
            $table->index(['parent_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_arguments');
        Schema::dropIfExists('discussion_position_claims');
        Schema::dropIfExists('discussion_claims');
        Schema::dropIfExists('discussion_positions');
    }
};
