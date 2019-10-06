<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoPreordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('do_preorders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('preorder_id');
            $table->foreign('preorder_id')->references('id')->on('preorders');

            $table->unsignedInteger('varian_id');
            $table->foreign('varian_id')->references('id')->on('varians');

            $table->enum('status', ['menunggu','terima','tolak'])->default('menunggu');
            
            $table->unsignedInteger('dikirim_ke');
            $table->integer('jumlah');

            $table->mediumText('rincian');

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
        Schema::dropIfExists('do_preorders');
    }
}
