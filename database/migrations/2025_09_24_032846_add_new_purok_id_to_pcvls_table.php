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
            $table->unsignedBigInteger('new_purok_id')->nullable()->after('purok_id');
            $table->foreign('new_purok_id')->references('id')->on('puroks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcvls', function (Blueprint $table) {
            $table->dropForeign(['new_purok_id']);
            $table->dropColumn('new_purok_id');
        });
    }
};
