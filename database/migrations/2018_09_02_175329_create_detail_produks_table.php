<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailProduksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_produks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            // $table->integer('harga')->default(0);
            $table->mediumText('deskripsi');
            $table->double('berat', 15, 2)->default(0);
            $table->enum('satuan_berat', ["g", "kg"])->default("g");
            $table->unsignedInteger('kategori_id');
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
        Schema::dropIfExists('detail_produks');
    }
}
