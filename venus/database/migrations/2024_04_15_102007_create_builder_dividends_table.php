<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builder_dividends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('builder_id');
            $table->foreign('builder_id')->references('id')->on('users');
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');  
            $table->string('amount')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('builder_dividends');
    }
};
