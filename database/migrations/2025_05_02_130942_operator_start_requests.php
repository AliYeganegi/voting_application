<?php

// database/migrations/2025_05_02_000000_create_operator_start_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operator_start_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['pending','completed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('operator_start_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')
                  ->constrained('operator_start_requests')
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
        Schema::dropIfExists('operator_start_confirmations');
        Schema::dropIfExists('operator_start_requests');
    }
};

