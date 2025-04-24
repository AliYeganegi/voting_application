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
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id')->unique()->nullable()->after('email');
            $table->string('license_number')->nullable()->after('national_id');
            $table->string('profile_image')->nullable()->after('license_number');
            $table->boolean('is_candidate')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('national_id');
            $table->dropColumn('license_id');
            $table->dropColumn('profile_image');
            $table->dropColumn('is_candidate');
        });
    }
};
