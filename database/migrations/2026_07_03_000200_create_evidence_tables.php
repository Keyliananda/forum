<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained('discussion_claims')->cascadeOnDelete();
            $table->foreignId('argument_id')->nullable()->constrained('discussion_arguments')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('url')->nullable();
            $table->string('doi')->nullable();
            $table->string('title');
            $table->string('publisher')->nullable();
            $table->date('published_at')->nullable();
            $table->date('accessed_at')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('locator')->nullable();
            $table->string('stance')->default('supports');
            $table->string('verification_status')->default('unverified');
            $table->timestamps();

            $table->index(['discussion_id', 'verification_status']);
            $table->index(['claim_id', 'verification_status']);
            $table->index(['argument_id', 'verification_status']);
        });

        Schema::create('evidence_verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evidence_id')->constrained('discussion_evidence')->cascadeOnDelete();
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['evidence_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_verifications');
        Schema::dropIfExists('discussion_evidence');
    }
};
