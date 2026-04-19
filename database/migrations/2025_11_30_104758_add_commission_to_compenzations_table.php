<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionToCompenzationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compenzations', function (Blueprint $table) {
            // Add commission field if it doesn't exist (matches legacy schema: varchar(4))
            // In modern Laravel, we use decimal for better precision
            if (!Schema::hasColumn('compenzations', 'commission')) {
                $table->decimal('commission', 10, 2)->nullable()->after('vat');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compenzations', function (Blueprint $table) {
            if (Schema::hasColumn('compenzations', 'commission')) {
                $table->dropColumn('commission');
            }
        });
    }
}
