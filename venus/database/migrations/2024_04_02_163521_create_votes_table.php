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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');        
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('attach_documents')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->enum('polling_option', ['Yes', 'No','Accept','Reject'])->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
          
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
        Schema::dropIfExists('votes');
    }
};
