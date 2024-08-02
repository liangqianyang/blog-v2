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
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement()->comment('主键ID');
            $table->string('user_name',64)->nullable(false)->default('')->comment('用户名');
            $table->string('nick_name',64)->nullable(false)->default('')->comment('昵称');
            $table->string('password',255)->nullable(false)->default('')->comment('密码');
            $table->string('email',64)->nullable(false)->default('')->comment('邮箱');
            $table->string('phone',64)->nullable(false)->default('')->comment('手机号');
            $table->string('avatar',255)->nullable(false)->default('')->comment('头像');
            $table->tinyInteger('status')->nullable(false)->default(1)->comment('状态 -1禁用 1正常');
            $table->timestamp('created_at')->nullable(false)->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->primary('id');
            $table->unique('user_name');
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
