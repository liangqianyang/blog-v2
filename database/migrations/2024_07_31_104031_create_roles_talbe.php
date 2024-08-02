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
        Schema::create('roles', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->autoIncrement()->comment('主键ID');
            $table->string('name', 64)->nullable(false)->default('')->comment('角色名称');
            $table->tinyInteger('state')->nullable(false)->default(1)->comment('状态 -1：禁用 1：正常');
            $table->timestamp('created_at')->nullable(false)->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->primary('id');
            $table->comment('角色表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
