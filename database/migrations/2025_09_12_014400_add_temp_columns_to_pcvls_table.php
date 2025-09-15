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
        Schema::table('pcvls', function (Blueprint $table) {
            $table->boolean('temp_is_kbbl')->nullable();
            $table->boolean('temp_is_kbpl')->nullable();
            $table->boolean('temp_is_kbpm')->nullable();
            $table->integer('temp_kbbl_id')->nullable();
            $table->integer('temp_kbpl_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcvls', function (Blueprint $table) {
            $table->dropColumn(['temp_is_kbbl', 'temp_is_kbpl', 'temp_is_kbpm', 'temp_kbbl_id', 'temp_kbpl_id']);
        });
    }
};
