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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties'); 
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->unsignedBigInteger('reciever_id');
            $table->foreign('reciever_id')->references('id')->on('users');
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
        Schema::dropIfExists('transfers');
    }
};
