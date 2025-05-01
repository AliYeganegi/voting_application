<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ballot_candidate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ballot_id')
                  ->constrained('ballots')
                  ->onDelete('cascade');
            $table->foreignId('candidate_id')
                  ->constrained('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballot_candidate');
    }
};
