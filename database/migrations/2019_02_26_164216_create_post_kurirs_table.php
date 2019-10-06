<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostKurirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_kurirs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kurir_id');
            $table->foreign('kurir_id')->references('id')->on('kurirs');
            
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
        Schema::dropIfExists('post_kurirs');
    }
}
