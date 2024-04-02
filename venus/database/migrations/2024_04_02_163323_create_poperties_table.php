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
        Schema::create('poperties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $tbale->string('images')->nullable();
            $tbale->string('property_name')->nullable();
            $table->enum('property_type', ['Rental', 'Land'])->nullable();
            $tbale->string('property size')->nullable();
            $tbale->string('rental_price')->nullable();
            $table->enum('rental_frequency', ['Monthly', 'Annual'])->nullable();
            $tbale->string('no_of_bedrooms')->nullable();
            $tbale->string('amenities')->nullable();
            $tbale->string('description')->nullable();
            $tbale->string('verification_details')->nullable();
            $tbale->string('property_address')->nullable();
            $tbale->string('project_completion_date')->nullable();
            $table->enum('floor', ['Groundfloor', 'first floor','Second floor','Basement'])->nullable();
            $tbale->string('govt_assessed_land')->nullable();
            $tbale->string('attachment')->nullable()->change();
            $tbale->string('amenities')->nullable();
            $tbale->string('amenities')->nullable();
            $tbale->string('amenities')->nullable();
            $tbale->string('amenities')->nullable();
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
        Schema::dropIfExists('poperties');
    }
};
