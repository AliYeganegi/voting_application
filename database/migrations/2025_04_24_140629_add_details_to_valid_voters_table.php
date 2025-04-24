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
        Schema::table('valid_voters', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('voter_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('license_number')->nullable()->after('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('valid_voters', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('license_number');
        });
    }
};
