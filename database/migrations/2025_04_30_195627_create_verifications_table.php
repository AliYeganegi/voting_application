<?php
// database/migrations/2025_04_30_195627_create_verifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();

            // FK to the session
            $table->foreignId('voting_session_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('voter_id');    // national ID in plain text
            $table->string('voter_hash');  // sha256(voter_id)

            // Use DATETIME so MySQL doesn’t demand a default
            $table->dateTime('started_at');
            $table->dateTime('expires_at');

            $table->enum('status', ['pending','used','expired'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
