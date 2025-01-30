<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImplementationAgreementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('implementation_agreement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compenzation');
            $table->unsignedDecimal('discount', 5, 2);
            $table->boolean('with_ddv');
            $table->unsignedDecimal('discount_amount', 10, 2);
            $table->unsignedDecimal('discount_ddv_amount', 10, 2);
            $table->unsignedDecimal('net_amount', 10, 2);
            $table->unsignedDecimal('transfer_amount', 10, 2);
            $table->timestamps();

            $table->foreign('id_compenzation')
                  ->references('id')
                  ->on('compenzations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('implementation_agreement');
    }
}
