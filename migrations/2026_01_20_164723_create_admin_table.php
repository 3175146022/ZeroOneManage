<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('snowflake_id')->nullable(false)->default(0)->unique()->comment('雪花ID');
            $table->string('username', 20)->nullable(false)->default('')->unique()->comment('用户名');
            $table->string('nickname', 50)->nullable(false)->default('')->comment('昵称');
            $table->string('password', 32)->nullable(false)->default('')->comment('密码');
            $table->string('salt', 6)->nullable(false)->default('')->comment('密码盐');
            $table->string('avatar', 255)->nullable(false)->default('')->comment('头像');
            $table->string('email', 100)->nullable(false)->default('')->comment('邮箱');
            $table->string('mobile', 15)->nullable(false)->default('')->comment('手机号');
            $table->unsignedTinyInteger('login_failure')->nullable(false)->default(0)->comment('登录失败次数');
            $table->timestamp('login_time')->nullable()->default(null)->comment('登录时间');
            $table->string('login_ip', 45)->nullable(false)->default('')->comment('登录IP');
            $table->string('token', 64)->nullable(false)->default('')->comment('登录Token');
            $table->string('refresh_token', 64)->nullable(false)->default('')->comment('刷新Token');
            $table->unsignedTinyInteger('status')->nullable(false)->default(1)->comment('状态：1-启用 2-禁用');
            $table->unsignedTinyInteger('is_super')->nullable(false)->default(1)->comment('超级管理权限：1-是 2-否');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('管理员表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
