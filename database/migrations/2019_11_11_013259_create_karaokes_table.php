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
            $table->string('phone',13);
            $table->string('price',30);
            $table->string('time_open',10);
            $table->string('rating',4);
            $table->decimal('ltn',10,8);
            $table->decimal('lgn',11,8);
            $table->text('album');
            $table->text('video');
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
