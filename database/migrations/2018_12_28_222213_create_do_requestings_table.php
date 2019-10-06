<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoRequestingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('do_requestings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('requesting_id');
            $table->foreign('requesting_id')->references('id')->on('requestings');

            $table->unsignedInteger('varian_id');
            $table->foreign('varian_id')->references('id')->on('varians');

            $table->enum('status', ['menunggu','terima','tolak'])->default('menunggu');

            $table->unsignedInteger('dibeli_dari');
            $table->foreign('dibeli_dari')->references('id')->on('kotas');
            
            $table->string('dikirim_dari');
            $table->bigInteger('estimasi_pengiriman');

            $table->double('berat', 15, 2)->default(0);
            $table->enum('satuan_berat', ["g", "kg"])->default("g");

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
        Schema::dropIfExists('do_requestings');
    }
}
