<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBraintreeCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('braintree_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->morphs('holder');
            $table->string('token');
            $table->string('card_type');
            $table->integer('bin');
            $table->integer('last_four');
            $table->string('card_holder')->nullable();
            $table->timestamp('expiration_date');
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
        Schema::dropIfExists('braintree_credit_cards');
    }
}
