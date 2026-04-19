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
        Schema::table('implementation_agreement', function (Blueprint $table) {
            // Datum podpisa sporazuma
            $table->date('signed_date')->nullable()->after('transfer_amount');
            
            // Datum veljavnosti sporazuma
            $table->date('valid_from')->nullable()->after('signed_date');
            $table->date('valid_until')->nullable()->after('valid_from');
            
            // Status sporazuma
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])
                  ->default('draft')
                  ->after('valid_until');
            
            // Referenčna številka sporazuma
            $table->string('reference_number', 50)->nullable()->after('status');
            
            // Opombe
            $table->text('notes')->nullable()->after('reference_number');
            
            // Kdo je podpisal sporazum
            $table->string('signed_by', 100)->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('implementation_agreement', function (Blueprint $table) {
            $table->dropColumn([
                'signed_date',
                'valid_from',
                'valid_until',
                'status',
                'reference_number',
                'notes',
                'signed_by'
            ]);
        });
    }
};
