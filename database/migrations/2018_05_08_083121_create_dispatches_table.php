<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dispatchNo');
            $table->string('referenceNo')->nullable();
            $table->date('dispatchStartingDate')->nullable();
            $table->string('invoiceNo')->nullable();
            $table->date('invoiceDate')->nullable();
            $table->string('totalAmount')->nullable();
            $table->string('totalExpense')->nullable();
            $table->integer('client_id');
            $table->string('trackingIDNo')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('dispatches');
    }
}
