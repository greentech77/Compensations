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
            // Status predloga
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'cancelled'])
                  ->default('draft')
                  ->after('file_name');
            
            // Datum pošiljanja predloga
            $table->date('sent_date')->nullable()->after('status');
            
            // Datum odobritve/zavrnitve
            $table->date('response_date')->nullable()->after('sent_date');
            
            // Opombe/razlog zavrnitve
            $table->text('notes')->nullable()->after('response_date');
            
            // Kdo je odobril/zavrnil (user_id)
            $table->unsignedBigInteger('approved_by')->nullable()->after('notes');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compenzations_proposals', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status',
                'sent_date',
                'response_date',
                'notes',
                'approved_by'
            ]);
        });
    }
};
