<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default(0);//用户id
            $table->integer('oid')->default(0);//order-id

            $table->char('asin', 24)->default('');//购买的asin
            $table->tinyInteger('from_site')->default(0); // 来自站点
            $table->char('eid', 50)->default('');//评价详情id
            $table->string('url', 500)->default('');//推广链接

            $table->tinyInteger('type')->default(1); // 1up 2down
            $table->smallInteger('num')->default(0);//zan
            $table->integer('golds')->default(0);//金币

            $table->tinyInteger('status')->default(1);//-1已退款 0失败 1进行中 3已同步 3成功
            $table->timestamps();

            //索引
            $table->index(['uid', 'from_site']);
            $table->index(['uid', 'status']);
            $table->index(['uid', 'asin']);
            $table->index(['uid', 'eid']);
            $table->index(['oid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}