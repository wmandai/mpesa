<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaB2cResultParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_b2c_result_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('response_id');
            $table->decimal('transaction_amount', 10, 2);
            $table->string('transaction_receipt')->unique();
            $table->char('recipient_is_registered_customer', 1);
            $table->bigInteger('charges_paid_available_balance');
            $table->string('receiver_public_name');
            $table->dateTime('transaction_completed_time');
            $table->decimal('utility_account_balance', 10, 2);
            $table->decimal('working_account_balance', 10, 2);
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
        Schema::dropIfExists('mpesa_b2c_result_parameters');
    }
}
