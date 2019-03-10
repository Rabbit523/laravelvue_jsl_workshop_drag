<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaTripPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('area_trip', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('area_id')->unsigned()->index();
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->integer('trip_id')->unsigned()->index();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->date('tripStartDate')->nullable();
            $table->integer('dispatch_id');
            $table->string('sequence')->nullable();
            
            $table->date('tripEndDate')->nullable();
            $table->string('consignmentNo')->nullable();
            $table->string('detailsofGoods')->nullable();
            $table->string('weightandDimmension')->nullable();
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
        Schema::drop('area_trip');
    }
}
