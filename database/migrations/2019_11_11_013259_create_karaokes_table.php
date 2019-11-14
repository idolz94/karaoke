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
            $table->char('name')->nullable();
            $table->text('avatar')->nullable();
            $table->char('city')->nullable();
            $table->char('district')->nullable();
            $table->string('address')->nullable();
            $table->char('phone',13)->nullable();
            $table->string('price',30)->nullable();
            $table->char('time_open',10)->nullable();
            $table->char('rating',10)->nullable();
            $table->decimal('ltn',10,8)->nullable();
            $table->decimal('lgn',11,8)->nullable();
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
