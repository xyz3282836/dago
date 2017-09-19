<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name',50)->default('name')->comment('标识');
            $table->char('desc',50)->default('会员')->comment('前台显示');
            $table->char('icon',50)->default('icon')->comment('前台显示icon');

            $table->integer('once')->default('1000')->comment('单次金币');
            $table->integer('more')->default('1000')->comment('累加金币');
            $table->tinyInteger('type')->default('1')->comment('1单次2累加');

            $table->decimal('service_one_rate', 10, 2)->default('0.1')->comment('限时下单服务费率');
            $table->decimal('service_three_rate', 10, 2)->default('0.1')->comment('普通下单服务费率');
            $table->integer('service_one_min')->default('1000')->comment('限时下单最少金币');
            $table->integer('service_three_min')->default('1000')->comment('普通下单最少金币');
            $table->integer('weight')->default('1')->comment('评价权重');

            $table->integer('gold_recharge')->default('100')->comment('最少充值金币');
            $table->integer('gold_step')->default('100')->comment('金币充值步长');
            $table->integer('balance_recharge')->default('100')->comment('最少充值余额');
            $table->integer('balance_step')->default('100')->comment('余额充值步长');

            $table->tinyInteger('status')->default('1')->comment('0禁用1可用');
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
        Schema::dropIfExists('roles');
    }
}
