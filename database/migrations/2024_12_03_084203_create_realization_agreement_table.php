<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealizationAgreementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_agreement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compenzation');
            $table->decimal('commission', 10, 2)->unsigned();
            $table->decimal('commission_amount', 10, 2)->unsigned();
            $table->decimal('commission_ddv_amount', 10, 2)->unsigned();
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
        Schema::dropIfExists('realization_agreement');
    }
}
