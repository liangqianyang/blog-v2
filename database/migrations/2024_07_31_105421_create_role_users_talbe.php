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
        Schema::create('role_users', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement()->comment('主键ID');
            $table->bigInteger('role_id')->nullable(false)->unsigned()->default(0)->comment('角色ID');
            $table->bigInteger('user_id')->nullable(false)->unsigned()->default(0)->comment('用户ID');
            $table->tinyInteger('state')->nullable(false)->default(1)->comment('状态 -1:删除 1:正常');
            $table->primary('id');
            $table->index('role_id');
            $table->index('user_id');
            $table->comment('角色用户关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_users');
    }
};
