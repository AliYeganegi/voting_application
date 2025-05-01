<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ballots', function (Blueprint $table) {
            $table->id();
            // link to the voting session
            $table->foreignId('voting_session_id')
                  ->constrained()
                  ->onDelete('cascade');
            // anonymous hash (no personal data)
            $table->string('voter_hash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
