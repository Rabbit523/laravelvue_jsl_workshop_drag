<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToAdvancepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advancepayments', function ($table) {
            $table->integer('bill_id')->nullable();
            $table->integer('equipment_id');
            $table->date('voucherDate');
            $table->string('referenceNumber')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advancepayments', function (Blueprint $table) {
            //
        });
    }
}
