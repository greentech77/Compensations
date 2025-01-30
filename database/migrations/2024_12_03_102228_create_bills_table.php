<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_entity');
            $table->decimal('amount', 8, 2);
            $table->integer('year', false, true)->length(4);
            $table->date('date');
            $table->timestamps();

            $table->foreign('id_entity')
                ->references('id')
                ->on('entities'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
