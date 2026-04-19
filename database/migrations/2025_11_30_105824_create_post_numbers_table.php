<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('post_numbers')) {
            Schema::create('post_numbers', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('code')->length(4)->unique()->comment('Poštna številka (4 mesta)');
                $table->string('postname', 50)->comment('Naziv pošte');
                $table->timestamps();
                
                $table->index('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_numbers');
    }
}
