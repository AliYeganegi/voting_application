<?php

// database/migrations/2025_05_02_000000_create_operator_approvals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('operator_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_session_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('operator_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('action', ['start','end']);
            $table->timestamps();
            $table->unique(['voting_session_id','operator_id','action'], 'op_uniq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('operator_approvals');
    }
};

