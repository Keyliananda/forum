<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key')->nullable()->index();
            $table->string('name');
            $table->json('weights');
            $table->timestamps();
        });

        Schema::create('discussion_result_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->string('profile_key');
            $table->json('weights');
            $table->json('position_scores');
            $table->json('breakdown');
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->index(['discussion_id', 'profile_key', 'computed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_result_snapshots');
        Schema::dropIfExists('result_profiles');
    }
};
