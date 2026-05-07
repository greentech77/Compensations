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
            $table->decimal('discount', 5, 2)->unsigned();
            $table->boolean('with_ddv');
            $table->decimal('discount_amount', 10, 2)->unsigned();
            $table->decimal('discount_ddv_amount', 10, 2)->unsigned();
            $table->decimal('net_amount', 10, 2)->unsigned();
            $table->decimal('transfer_amount', 10, 2)->unsigned();
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
