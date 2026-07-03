<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_signals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('label');
            $table->unsignedInteger('up_count')->default(0);
            $table->unsignedInteger('down_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->string('source_url')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->index(['discussion_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_signals');
    }
};
