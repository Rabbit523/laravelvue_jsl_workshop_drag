<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customerName');
            $table->string('orderNumber')->nullable();
            $table->integer('suburb');
            $table->text('description')->nullable();
            $table->boolean('clearAccess');
            $table->text('deliveryLocation')->nullable();
            $table->boolean('upstairs');
            $table->boolean('chargedForUpstairs')->nullable();
            $table->string('bookingType');
            $table->date('bookingDate');
            $table->time('startingTime')->nullable();
            $table->time('endingTime')->nullable();
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
        Schema::dropIfExists('bookings');
    }
}
