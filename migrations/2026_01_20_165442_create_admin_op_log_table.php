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
        Schema::create('admin_op_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('snowflake_id')->nullable(false)->default(0)->unique()->comment('雪花ID');
            $table->unsignedBigInteger('admin_id')->nullable(false)->default(0)->comment('管理员ID');
            $table->string('username', 50)->nullable(false)->default('')->comment('管理员用户名');
            $table->string('method', 10)->nullable(false)->default('')->comment('请求方法');
            $table->string('path', 255)->nullable(false)->default('')->comment('请求路径');
            $table->string('title', 100)->nullable(false)->default('')->comment('操作标题');
            $table->longText('body')->nullable(false)->comment('请求内容体');
            $table->string('ip', 45)->nullable(false)->default('')->comment('请求IP');
            $table->string('user_agent', 255)->nullable(false)->default('')->comment('User-Agent');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('管理员操作日志表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_op_log');
    }
};
