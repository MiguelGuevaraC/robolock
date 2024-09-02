<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration 
{
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('photoPath');
            $table->boolean('state')->default(1)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
   
            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');

        });
    }

    public function down()
    {
        Schema::dropIfExists('photos');
    }


};
