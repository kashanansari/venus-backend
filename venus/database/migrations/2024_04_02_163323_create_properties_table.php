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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->json('images')->nullable();
            $table->string('property_name')->nullable();
            $table->enum('property_type', ['Rental', 'Land'])->nullable();
            $table->string('property_size')->nullable();
            $table->string('rental_price')->nullable();
            $table->enum('rental_frequency', ['Monthly', 'Annual'])->nullable();
            $table->string('no_of_bedrooms')->nullable();
            $table->string('amenities')->nullable();
            $table->string('description')->nullable();
            $table->string('verification_details')->nullable();
            $table->string('property_address')->nullable();
            $table->string('project_completion_date')->nullable();
            $table->enum('floor', ['Ground floor', 'first floor','Second floor','Basement'])->nullable();
            $table->string('govt_assessed_land')->nullable();
            $table->string('attachment')->nullable()->change();
            $table->string('cap')->nullable();
            $table->string('annual_recurring_avenue')->nullable();
            $table->string('dividend')->nullable();
            $table->string('declaration')->nullable();
            $table->string('buider_wallet_address')->nullable();
            $table->string('min_amount')->nullable();
            $table->string('max_amount')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
