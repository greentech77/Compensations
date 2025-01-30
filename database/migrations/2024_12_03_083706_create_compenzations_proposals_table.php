<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompenzationsProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compenzations_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compenzation');
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
        Schema::dropIfExists('compenzations_proposals');
    }
}
