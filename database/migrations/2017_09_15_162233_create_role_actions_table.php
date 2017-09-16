<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rid')->default('1')->comment('角色id');
            $table->integer('aid')->default('1')->comment('动作id');
            $table->integer('service_gold')->default('1000')->comment('指定金币');
            $table->decimal('service_rate', 10, 2)->default('0.1')->comment('百分比');
            $table->tinyInteger('type')->default('1')->comment('1指定金币2百分比');
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
        Schema::dropIfExists('role_actions');
    }
}
