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
        // Add file fields to implementation_agreement table
        Schema::table('implementation_agreement', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('transfer_amount');
            $table->string('file_name')->nullable()->after('file_path');
        });

        // Add file fields to realization_agreement table
        Schema::table('realization_agreement', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('transfer_amount');
            $table->string('file_name')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove file fields from implementation_agreement table
        Schema::table('implementation_agreement', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name']);
        });

        // Remove file fields from realization_agreement table
        Schema::table('realization_agreement', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name']);
        });
    }
};
