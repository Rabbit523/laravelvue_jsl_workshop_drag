<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTripPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_trip', function (Blueprint $table) {
            $table->integer('equipment_id')->unsigned()->index();
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->integer('trips_id')->unsigned()->index();
            $table->foreign('trips_id')->references('id')->on('trips')->onDelete('cascade');
            $table->integer('dispatch_id')->unsigned()->index();
            $table->foreign('dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
            $table->primary(['equipment_id', 'trips_id', 'dispatch_id']);
            $table->integer('replacedby_id')->nullable();
            $table->string('reason')->nullable();
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
        Schema::drop('equipment_trip');
    }
}
