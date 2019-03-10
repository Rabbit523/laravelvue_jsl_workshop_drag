<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('trips', function($table) {
                $table->integer('staff1')->nullable();
                $table->integer('staff2')->nullable();
                $table->integer('staff3')->nullable();
                $table->boolean('hasLog')->default(0);
                $table->integer('createdBy')->nullable();
                $table->integer('updatedBy')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
