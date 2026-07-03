<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussions', function (Blueprint $table): void {
            $table->string('governance_profile')->default('open_democratic')->after('status')->index();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('reportable');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['reporter_id', 'created_at']);
        });

        Schema::create('moderation_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('actionable');
            $table->string('action');
            $table->text('public_reason')->nullable();
            $table->text('internal_note')->nullable();
            $table->string('policy_version')->default('w5.1');
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('reports');

        Schema::table('discussions', function (Blueprint $table): void {
            $table->dropColumn('governance_profile');
        });
    }
};
