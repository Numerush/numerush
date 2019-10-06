<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelationTableKota extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requestings', function (Blueprint $table) {
            $table->foreign('dibeli_dari')->references('id')->on('kotas');
            // $table->foreign('dikirim_ke')->references('id')->on('kotas');
        });
        Schema::table('preorders', function (Blueprint $table) {
            $table->foreign('dibeli_dari')->references('id')->on('kotas');
            // $table->foreign('dikirim_dari')->references('id')->on('kotas');
        });
        Schema::table('trips', function (Blueprint $table) {
            
            $table->foreign('kota_asal')->references('id')->on('kotas');
            $table->foreign('kota_tujuan')->references('id')->on('kotas');
            // $table->foreign('dikirim_dari')->references('id')->on('kotas');
        });
        // Schema::table('do_requestings', function (Blueprint $table) {
        //     $table->foreign('dikirim_dari')->references('id')->on('kotas');
        // });
        // Schema::table('do_preorders', function (Blueprint $table) {
        //     $table->foreign('dikirim_dari')->references('id')->on('kotas');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requestings', function (Blueprint $table) {
            $table->dropForeign(['dibeli_dari']);
        });
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropForeign(['dibeli_dari']);
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['kota_asal']);
            $table->dropForeign(['kota_tujuan']);
        });
    }
}
