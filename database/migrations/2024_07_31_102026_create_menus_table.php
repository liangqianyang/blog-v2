<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->nullable(false)->unsigned()->autoIncrement()->comment('主键ID');
            $table->bigInteger('parent_id')->nullable(false)->default(0)->unsigned()->comment('父级ID');
            $table->string('name',64)->nullable(false)->default('')->comment('菜单名称');
            $table->string('component',100)->nullable(false)->default('')->comment('组件名称');
            $table->string('path',100)->nullable(false)->default('')->comment('路由地址');
            $table->string('icon',100)->nullable(false)->default('')->comment('图标');
            $table->smallInteger('sort')->unsigned()->nullable(false)->default(1)->comment('排序');
            $table->tinyInteger('state')->nullable(false)->default(1)->comment('状态 -1:删除 1:正常');
            $table->timestamp('created_at')->nullable(false)->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->index('parent_id');
            $table->index('name');
            $table->comment('菜单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
