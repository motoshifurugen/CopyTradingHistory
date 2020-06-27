<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('entry_dateTime');
            $table->dateTime('exit_dateTime');
            $table->string('symbol');
            // enumは複数の変数に定数を割り当てる
            $table->enum('order_type', ['BUY', 'SELL']);
            $table->integer('profit');
            // double('カラム名', '合計桁数', '小数点以下の桁数')
            $table->double('amount', 10, 3);
            $table->double('entry', 13, 5);
            $table->double('exit', 13, 5);
            $table->text('memo')->nullable(true);
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
        Schema::dropIfExists('trading_histories');
    }
}
