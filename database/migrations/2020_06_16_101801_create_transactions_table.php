<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('label');
            $table->decimal('amount', 19, 4);
            $table->dateTime('transaction_at');
            $table->timestamps();

            $table->index(['transaction_at', 'user_id']);
        });

        // this way of creating index is compatible both with sqlite and mysql 8+
        \DB::unprepared('
            create index transactions_date_transaction_at_user_id on transactions ((date(transaction_at)), user_id)
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
