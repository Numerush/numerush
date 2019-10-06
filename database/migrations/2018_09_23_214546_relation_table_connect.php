<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelationTableConnect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requestings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('detail_produk_id')->references('id')->on('detail_produks');
        });
        Schema::table('preorders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('detail_produk_id')->references('id')->on('detail_produks');
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('detail_produks', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategoris');
        });
        Schema::table('gambars', function (Blueprint $table) {
            $table->foreign('detail_produk_id')->references('id')->on('detail_produks');
        });
        // Schema::table('kategoris', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users');
        // });
        // Schema::table('do_requestings', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('status_transaksi_id')->references('id')->on('status_transaksis');
        //     $table->foreign('requesting_id')->references('id')->on('requestings');
        // });
        // Schema::table('do_preorders', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('status_transaksi_id')->references('id')->on('status_transaksis');
        //     $table->foreign('preorder_id')->references('id')->on('preorders');
        // });
        Schema::table('notifikasi_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('notifikasi_id')->references('id')->on('notifikasis');
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
            $table->dropForeign(['user_id']);
            $table->dropForeign(['detail_produk_id']);
        });
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['detail_produk_id']);
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('gambars', function (Blueprint $table) {
            $table->dropForeign(['detail_produk_id']);
        });
        // Schema::table('kategoris', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        // // });
        // Schema::table('do_requestings', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        //     $table->dropForeign(['status_transaksi_id']);
        //     $table->dropForeign(['requesting_id']);
        // });
        // Schema::table('do_preorders', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        //     $table->dropForeign(['status_transaksi_id']);
        //     $table->dropForeign(['preorder_id']);
        // });
        Schema::table('notifikasi_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            // $table->dropForeign(['notifikasi_id']);
        });
    }
}
