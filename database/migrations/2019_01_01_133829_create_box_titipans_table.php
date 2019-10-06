<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoxTitipansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_titipans', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('shopper_id');
            $table->foreign('shopper_id')->references('id')->on('users');

            $table->integer('harga');
            $table->double('berat', 15, 2)->default(0);

            $table->bigInteger('estimasi_pengiriman');

            $table->unsignedInteger('dibeli_dari');
            $table->foreign('dibeli_dari')->references('id')->on('kotas');
            
            $table->unsignedInteger('varian_id');
            $table->foreign('varian_id')->references('id')->on('varians');
            
            $table->string('dikirim_dari');
            $table->unsignedInteger('dikirim_ke');

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
        Schema::dropIfExists('box_titipans');
    }
}
