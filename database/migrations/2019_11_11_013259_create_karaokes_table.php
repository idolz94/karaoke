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
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('avatar');
            $table->string('city',30);
            $table->string('district',50);
            $table->string('address');
            $table->string('phone',13)->nullable();
            $table->string('price',30)->nullable();
            $table->string('time_open',10)->nullable();
            $table->string('rating',4);
            $table->decimal('ltn',10,8);
            $table->decimal('lgn',11,8);
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
