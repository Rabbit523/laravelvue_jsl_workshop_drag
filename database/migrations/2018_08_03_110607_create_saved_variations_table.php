<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavedVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id');
            $table->integer('dispatch_id');
            $table->integer('staff_id');
            $table->integer('variation_id');
            $table->decimal('amount');
            $table->integer('multiplier');
            $table->string('paidTo');
            $table->string('type');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('saved_variations');
    }
}
