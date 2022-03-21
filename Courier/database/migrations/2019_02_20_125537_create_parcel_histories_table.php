<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('sender_address');
            $table->string('current_address');
        });

        Schema::create('parcel_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parcel_id');
            $table->foreign('parcel_id')
                ->references('id')->on('parcels');
            $table->timestamp('date_of_action');
            $table->string('state_of_delivery');
            $table->string('localisation');
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
            $table->dropColumn('sender_address');
            $table->dropColumn('current_address');
        });
        Schema::dropIfExists('parcel_histories');
    }
}
