<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitipansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titipans', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('shopper_id');
            $table->foreign('shopper_id')->references('id')->on('users');
            
            $table->integer('total_harga');
            $table->integer('total_harga_kirim');
            $table->string('metode_bayar');

            $table->bigInteger('estimasi_pengiriman');

            $table->string('nomer_resi')->default("");
            $table->string('bukti_bayar')->default("");
            $table->boolean('status_bayar')->default(false);
            $table->unsignedInteger('kurir_id');

            
            $table->unsignedInteger('dibeli_dari');
            $table->foreign('dibeli_dari')->references('id')->on('kotas');
            
            $table->string('dikirim_dari');
            $table->unsignedInteger('dikirim_ke');

            $table->unsignedInteger('status_transaksi_id');
            $table->foreign('status_transaksi_id')->references('id')->on('status_transaksis');
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
        Schema::dropIfExists('titipans');
    }
}
