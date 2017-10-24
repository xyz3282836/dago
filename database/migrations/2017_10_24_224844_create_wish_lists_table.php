<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWishListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wish_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default(0);//用户id
            $table->integer('oid')->default(0);//order-id

            $table->char('asin', 24)->default('');//购买的asin
            $table->tinyInteger('from_site')->default(0); // 来自站点
            $table->string('keywords', 100)->default('');//keywords
            $table->date('start');//开始日期
            $table->date('end');//结束日期

            $table->smallInteger('num')->default(0);//zan
            $table->integer('golds')->default(0);//金币

            $table->tinyInteger('status')->default(1);//-1已退款 0失败 1进行中 3已同步 3成功
            $table->timestamps();

            //索引
            $table->index(['uid', 'from_site']);
            $table->index(['uid', 'status']);
            $table->index(['uid', 'asin']);
            $table->index(['uid', 'keywords']);
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
        Schema::dropIfExists('wish_lists');
    }
}
