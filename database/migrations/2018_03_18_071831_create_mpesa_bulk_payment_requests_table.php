<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaBulkPaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_bulk_payment_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unique('conversation_id');
            $table->string('originator_conversation_id');
            $table->decimal('amount', 10, 2);
            $table->string('phone', 20);
            $table->string('remarks')->nullable();
            $table->string('command_id')->default('BusinessPayment');
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('mpesa_bulk_payment_requests');
    }
}
