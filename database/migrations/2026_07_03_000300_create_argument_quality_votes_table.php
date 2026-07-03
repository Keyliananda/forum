<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('argument_quality_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('argument_id')->constrained('discussion_arguments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('clarity');
            $table->unsignedTinyInteger('relevance');
            $table->unsignedTinyInteger('logic');
            $table->unsignedTinyInteger('source_usage');
            $table->unsignedTinyInteger('fairness');
            $table->unsignedTinyInteger('rebuttal_strength');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['argument_id', 'user_id']);
            $table->index(['argument_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('argument_quality_votes');
    }
};
