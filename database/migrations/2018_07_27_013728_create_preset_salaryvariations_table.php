<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresetSalaryvariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_salaryvariations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('presetName');
            $table->integer('salaryvariation_id')->unsigned()->index();
            $table->foreign('salaryvariation_id')->references('id')->on('salaryvariations')->onDelete('cascade');
            $table->string('driverAmount');
            $table->string('helperAmount');
            $table->string('operatorAmount');
            $table->string('supervisorAmount');
            $table->string('otherAmount');
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
        Schema::dropIfExists('preset_salaryvariations');
    }
}
