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
        Schema::create('shop_networks', function (Blueprint $table) {
            $table->id();
            $table->string("shop_name");
            $table->string("name");
            $table->string("phone");
            $table->unsignedBigInteger("area_id");
            $table->text("address");
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
        Schema::dropIfExists('shop_networks');
    }
};
