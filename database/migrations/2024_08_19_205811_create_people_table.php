<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('typeofDocument')->nullable();
            $table->string('documentNumber')->unique()->nullable();
            $table->string('names')->nullable();
            $table->string('fatherSurname')->nullable();
            $table->string('motherSurname')->nullable();
            $table->date('dateBirth')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('status')->nullable();
            $table->boolean('state')->default(1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('people');
    }
};

