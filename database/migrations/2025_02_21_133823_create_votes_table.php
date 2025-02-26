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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('device_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('fingerprint')->nullable();

            $table->timestamps();

            // Ensure users, devices, sessions, and fingerprints cannot vote twice for the same poll
            $table->unique(['poll_id', 'user_id']);
            $table->unique(['poll_id', 'device_id']);
            $table->unique(['poll_id', 'session_id']);
            $table->unique(['poll_id', 'fingerprint']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
