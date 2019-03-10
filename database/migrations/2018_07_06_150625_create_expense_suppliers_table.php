<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('supplierName');
            $table->string('accountNumber')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('contactPerson');
            $table->string('address')->nullable();
            $table->string('emailAddress')->nullable();
            $table->string('website')->nullable();
            $table->string('creditLimit')->nullable();
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
        Schema::dropIfExists('expense_suppliers');
    }
}
