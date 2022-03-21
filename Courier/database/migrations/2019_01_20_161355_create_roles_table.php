<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->integer('courier_id')
                ->nullable()->unsigned();
            $table->foreign('courier_id')
                ->references('id')->on('users');
                //->onUpdate('cascade')
               // ->onDelete('cascade');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->nullable()->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->integer('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropForeign('parcels_courier_id_foreign');
            //$table->dropForeign(['courier_id']);
            $table->dropColumn('courier_id');
        });
        Schema::dropIfExists('roles');
    }
}
