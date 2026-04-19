<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyAmountPrecisionInCompenzationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid Doctrine DBAL requirement
        DB::statement('ALTER TABLE compenzations MODIFY amount DECIMAL(10, 2) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original precision if needed
        DB::statement('ALTER TABLE compenzations MODIFY amount DECIMAL(10, 4) NOT NULL');
    }
}
