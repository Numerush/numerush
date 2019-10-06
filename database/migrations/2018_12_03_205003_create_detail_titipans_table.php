<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailTitipansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_titipans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('harga');
            // $table->integer('harga_kirim');
            $table->unsignedInteger('titipan_id');
            $table->foreign('titipan_id')->references('id')->on('titipans');
            $table->unsignedInteger('varian_id');
            $table->foreign('varian_id')->references('id')->on('varians');

            $table->integer('postdata_id')->unsigned();
            $table->string('postdata_type');
            
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
        Schema::dropIfExists('detail_titipans');
    }
}
