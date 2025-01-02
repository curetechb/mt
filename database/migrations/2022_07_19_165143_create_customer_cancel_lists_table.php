<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCancelListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_cancel_lists', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->integer('total_order');
            $table->integer('cancel');
            $table->integer('delivery');
            $table->integer('nextday');
            $table->integer('processing');
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
        Schema::dropIfExists('customer_cancel_lists');
    }
}
