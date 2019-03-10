<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReceivingAdjustmentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_bills', function ( $table) {
            $table->text('remarks')->nullable();
            $table->date('receivingDate')->nullable();
            $table->decimal('adjustmentAmount', 11, 2)->nullable();
            $table->integer('status')->default(4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_bills', function (Blueprint $table) {
            //
        });
    }
}
