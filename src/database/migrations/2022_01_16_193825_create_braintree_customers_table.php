<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBraintreeCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('braintree_customers', function (Blueprint $table) {
            $table->id();
            $table->morphs('holder');
            $table->unsignedBigInteger('customer_id');
            $table->string('merchant_id');
            $table->string('global_id');
            $table->string('graphql_id');
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
        Schema::dropIfExists('braintree_customers');
    }
}
