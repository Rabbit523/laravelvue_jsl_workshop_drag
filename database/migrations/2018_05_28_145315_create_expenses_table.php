<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->date('expenseDate');
            $table->integer('dispatch_id')->unsigned()->index();
            $table->foreign('dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
            $table->integer('expenseType_id')->unsigned()->index();
            $table->foreign('expenseType_id')->references('id')->on('expensetypes')->onDelete('cascade');
            $table->integer('trip_id')->unsigned()->index();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->integer('equipment_id')->unsigned()->index();
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->decimal('actualAmount', 11, 2);
            $table->decimal('reportedAmount', 11, 2)->nullable();
            $table->boolean('flag')->nullable();
            $table->integer('flagBy')->nullable();
            $table->text('reasonToFlag')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('approvedBy')->nullable();
            $table->boolean('isApproved')->default(0);
            $table->boolean('status')->default(1);
            $table->integer('createdBy')->nullable();
            $table->integer('updatedBy')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
