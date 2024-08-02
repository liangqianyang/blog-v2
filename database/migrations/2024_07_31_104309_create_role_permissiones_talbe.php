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
        Schema::create('role_permissiones', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement()->comment('主键ID');
            $table->bigInteger('role_id')->unsigned()->nullable(false)->default(0)->comment('角色ID');
            $table->bigInteger('permission_id')->unsigned()->nullable(false)->default(0)->comment('权限ID');
            $table->primary('id');
            $table->index('role_id');
            $table->index('permission_id');
            $table->comment('角色权限关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissiones');
    }
};
