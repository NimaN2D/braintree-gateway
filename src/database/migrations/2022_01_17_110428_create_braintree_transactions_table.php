<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBraintreeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('braintree_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('holder');
            $table->foreignId('card_id')->constrained('braintree_credit_cards');
            $table->string('braintree_id');
            $table->string('currency_iso_code');
            $table->string('type');
            $table->float('amount');
            $table->string('status');
            $table->timestamp('braintree_created_at')->nullable();
            $table->timestamp('braintree_updated_at')->nullable();
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
        Schema::dropIfExists('braintree_transactions');
    }
}
