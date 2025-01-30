<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsCompenzationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills_compenzations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bill');
            $table->unsignedBigInteger('id_compenzation');
            $table->unsignedBigInteger('id_entity');
            $table->timestamps();

            $table->foreign('id_bill')
                  ->references('id')
                  ->on('bills');

            $table->foreign('id_compenzation')
                  ->references('id')
                  ->on('compenzations');

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
        Schema::dropIfExists('bills_compenzations');
    }
}
