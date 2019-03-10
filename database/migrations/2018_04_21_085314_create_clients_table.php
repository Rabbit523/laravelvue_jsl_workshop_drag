<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clientName');
            $table->string('accountNumber');
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('contactPerson');
            $table->string('address')->nullable();
            $table->string('emailAddress')->nullable();
            $table->string('website')->nullable();
            $table->string('creditLimit')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
