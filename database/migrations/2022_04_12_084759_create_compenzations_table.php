<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompenzationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compenzations', function (Blueprint $table) {
            $table->id();
            $table->char('name', 50);
            $table->integer('year', false, true)->length(4);
            $table->unsignedDecimal('amount', 10, 4);
            $table->integer('vat', false, true)->length(2)->default(22);
            $table->date('date');
            $table->date('date_finished');
            $table->date('date_payed');
            $table->boolean('storno')->nullable();
            $table->boolean('finished')->default(false);
            $table->boolean('with_ddv')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compenzations');
    }
}
