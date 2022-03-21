<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParcelSizeAndClientData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('mass')->nullable();
            $table->string('size')->nullable();
            $table->string('client_first_name');
            $table->string('client_last_name');
            $table->string('client_phone_number')->nullable();
            $table->string('client_email')->nullable();
            $table->string('sender_first_name');
            $table->string('sender_last_name');
            $table->string('sender_phone_number')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('parcel_content');

        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
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
            $table->dropColumn('mass');
            $table->dropColumn('size');
            $table->dropColumn('client_first_name');
            $table->dropColumn('client_last_name');
            $table->dropColumn('client_phone_number');
            $table->dropColumn('client_email');
            $table->dropColumn('sender_first_name');
            $table->dropColumn('sender_last_name');
            $table->dropColumn('sender_phone_number');
            $table->dropColumn('sender_email');
            $table->dropColumn('parcel_content');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
}
