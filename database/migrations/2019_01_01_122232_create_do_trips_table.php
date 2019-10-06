<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('do_trips', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips');

            $table->enum('status', ['menunggu','terima','tolak'])->default('menunggu');
            
            // $table->string('dibeli_dari');
            $table->unsignedInteger('dikirim_ke');
            $table->integer('jumlah');

            $table->unsignedInteger('varian_id');
            $table->foreign('varian_id')->references('id')->on('varians');

            $table->bigInteger('expired');
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
        Schema::dropIfExists('do_trips');
    }
}
