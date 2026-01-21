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
        Schema::create('auth_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pid')->nullable(false)->default(0)->comment('父级ID');
            $table->unsignedBigInteger('snowflake_id')->nullable(false)->default(0)->unique()->comment('雪花ID');
            $table->string('name', 100)->nullable(false)->default('')->comment('用户组名称');
            $table->string('code', 100)->nullable(false)->default('')->comment('角色代码');
            $table->text('rules')->nullable(false)->comment('用户组拥有的规则ID');
            $table->unsignedTinyInteger('status')->nullable(false)->default(1)->comment('状态：1-正常, 2-禁用');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户权限角色分组表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_group');
    }
};
