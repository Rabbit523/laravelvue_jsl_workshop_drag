<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentsTripKilometersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments_trip_kilometers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('startingDate');
            $table->date('endingDate');
            $table->string('equipmentNumber');
            $table->integer('equipment_id');
            $table->string('startingCoordinates');
            $table->string('endingCoordinates');
            $table->string('totalKilometers');
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
        Schema::dropIfExists('equipments_trip_kilometers');
    }
}
