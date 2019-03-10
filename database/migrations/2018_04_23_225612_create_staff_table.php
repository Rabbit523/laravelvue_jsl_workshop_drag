<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staffName');
            $table->string('homePhone')->nullable();
            $table->string('cellPhone')->nullable();
            $table->string('cnic')->nullable();
            $table->string('driversLicence')->nullable();
            $table->string('address')->nullable();
            $table->string('emergencyNo')->nullable();
            $table->string('driversLicense')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('staff');
    }
}
