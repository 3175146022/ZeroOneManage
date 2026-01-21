<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auth_rule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pid')->nullable(false)->default(0)->comment('父级ID');
            $table->unsignedBigInteger('snowflake_id')->nullable(false)->default(0)->unique()->comment('雪花ID');
            $table->unsignedTinyInteger('type')->nullable(false)->default(1)->comment('类型：1-目录 2-菜单 3-权限');
            $table->string('name', 100)->nullable(false)->default('')->comment('权限名称');
            $table->string('route_name', 255)->nullable(false)->default('')->comment('路由名称');
            $table->string('route_path', 255)->nullable(false)->default('')->comment('路由路径');
            $table->string('component', 255)->nullable(false)->default('')->comment('组件路径');
            $table->string('redirect', 255)->nullable(false)->default('')->comment('路由跳转地址');
            $table->string('perm', 255)->nullable(false)->default('')->comment('权限标识');
            $table->unsignedTinyInteger('always_show')->nullable(false)->default(1)->comment('【目录】只有一个子路由是否始终显示:1-是 2-否');
            $table->unsignedTinyInteger('keep_alive')->nullable(false)->default(1)->comment('【菜单】是否开启页面缓存:1-是 2-否');
            $table->string('icon', 50)->nullable(false)->default('')->comment('图标');
            $table->string('remark', 255)->nullable(false)->default('')->comment('备注');
            $table->unsignedInteger('weight')->nullable(false)->default(0)->comment('权重值，越大越靠前');
            $table->unsignedTinyInteger('status')->nullable(false)->default(1)->comment('状态：1-启用 2-禁用');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户权限规则表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_rule');
    }
};
