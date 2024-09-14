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
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement()->comment('主键ID');
            $table->string('name',64)->nullable(false)->default('')->comment('权限名称');
            $table->string('code',64)->nullable(false)->default('')->comment('权限标识');
            $table->string('description',128)->nullable(true)->default('')->comment('权限描述');
            $table->primary('id');
            $table->tinyInteger('state')->nullable(false)->default(1)->comment('状态 -1禁用 1正常');
            $table->timestamp('created_at')->nullable(false)->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->comment('权限表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissiones');
    }
};
