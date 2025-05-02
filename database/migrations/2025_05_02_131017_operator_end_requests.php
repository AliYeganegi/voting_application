<?php

// database/migrations/2025_05_02_000001_create_operator_end_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operator_end_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                  ->constrained('voting_sessions')
                  ->onDelete('cascade');
            $table->enum('status',['pending','completed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('operator_end_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')
                  ->constrained('operator_end_requests')
                  ->onDelete('cascade');
            $table->foreignId('operator_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['request_id','operator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_end_confirmations');
        Schema::dropIfExists('operator_end_requests');
    }
};
