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
        Schema::create('auth_group_relation', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable(false)->default(0)->comment('管理员ID');
            $table->unsignedBigInteger('group_id')->nullable(false)->default(0)->comment('用户组ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_group_relation');
    }
};
