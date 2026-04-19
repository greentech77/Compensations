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
        Schema::table('compenzations_proposals', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('id_compenzation');
            $table->string('file_name')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compenzations_proposals', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name']);
        });
    }
};
