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
        Schema::create('builder_kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('builder_id');
            $table->foreign('builder_id')->references('id')->on('users');
            $table->string('date_of_birth')->nullable();
            $table->string('cnic')->nullable();
            $table->string('licenese')->nullable();
            $table->string('passport')->nullable();
            $table->string('yearly_tax_report')->nullable();
            $table->string('nationality')->nullable();
            $table->string('res_address')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('occupation')->nullable();
            $table->string('source_of_funds')->nullable();
            $table->string('status')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
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
        Schema::dropIfExists('builder_kycs');
    }
};
