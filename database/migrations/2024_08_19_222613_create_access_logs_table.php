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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();

            $table->boolean('state')->default(1)->nullable();
            $table->string('status'); // Authorizado, No Authorizado
            $table->string('breakPoint');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('authorized_person_id')->nullable()->unsigned()->constrained('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
};
