<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kota_asal');
            $table->unsignedInteger('kota_tujuan');
            $table->bigInteger('tanggal_berangkat');
            $table->bigInteger('tanggal_kembali');
            $table->bigInteger('estimasi_pengiriman');
            $table->string('dikirim_dari');
            $table->mediumText('rincian');
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
        Schema::dropIfExists('trips');
    }
}
