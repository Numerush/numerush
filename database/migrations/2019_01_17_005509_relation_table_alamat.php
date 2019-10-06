<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelationTableAlamat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('titipans', function (Blueprint $table) {
            $table->foreign('dikirim_ke')->references('id')->on('alamats');
            $table->foreign('kurir_id')->references('id')->on('kurirs');
        });
        Schema::table('requestings', function (Blueprint $table) {
            $table->foreign('dikirim_ke')->references('id')->on('alamats');
        });
        Schema::table('do_preorders', function (Blueprint $table) {
            $table->foreign('dikirim_ke')->references('id')->on('alamats');
        });
        Schema::table('do_trips', function (Blueprint $table) {
            $table->foreign('dikirim_ke')->references('id')->on('alamats');
        });
        Schema::table('box_titipans', function (Blueprint $table) {
            $table->foreign('dikirim_ke')->references('id')->on('alamats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requestings', function (Blueprint $table) {
            $table->dropForeign(['dikirim_ke']);
        });
        Schema::table('do_preorders', function (Blueprint $table) {
            $table->dropForeign(['dikirim_ke']);
        });
        Schema::table('do_trips', function (Blueprint $table) {
            $table->dropForeign(['dikirim_ke']);
        });
        Schema::table('box_titipans', function (Blueprint $table) {
            $table->dropForeign(['dikirim_ke']);
        });
        Schema::table('titipans', function (Blueprint $table) {
            $table->dropForeign(['dikirim_ke']);
        });
    }
}
