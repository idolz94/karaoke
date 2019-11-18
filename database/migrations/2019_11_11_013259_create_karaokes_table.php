<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaraokesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karaokes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('avatar')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('time_open')->nullable();
            $table->string('rating',20)->nullable();
            $table->decimal('ltn',10,8)->nullable();
            $table->decimal('lgn',11,8)->nullable();
            $table->text('detail_url')->nullable();
            $table->text('album')->nullable();
            $table->text('video')->nullable();
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
        Schema::dropIfExists('karaokes');
    }
}
