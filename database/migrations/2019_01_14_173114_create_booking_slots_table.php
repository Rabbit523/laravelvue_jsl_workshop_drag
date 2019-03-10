<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bookingFor')->nullable(); //Created if in future we have more than 2 vehicle so we can enter id of vehicle
            $table->string('bookingType')->nullable(); //One Man or Two Man delivery
            $table->datetime('timeSlotStart');
            $table->datetime('timeSlotEnd');
            $table->time('buffer')->nullable();
            $table->integer('bookingsId')->nullable();
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
        Schema::dropIfExists('booking_slots');
    }
}
