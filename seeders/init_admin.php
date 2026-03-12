<?php

declare(strict_types=1);

use App\Http\Admin\Model\Admin;
use App\Http\Admin\Model\AuthGroup;
use App\Http\Admin\Model\AuthGroupRelation;
use App\Http\Common\Enums\Status;
use Hyperf\Database\Seeders\Seeder;

class InitAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 初始化超级管理员 添加一条超级管理员数据
        Admin::create([
            'snowflake_id' => 860481958264823808,
            'username' => 'admin',
            'nickname' => 'Admin',
            'password' => '8da049176991ee06d9d3d871f0d77517',
            'salt' => '214e8c',
            'avatar' => '/avatar.jpeg',
            'is_super' => 1,
        ]);

        AuthGroup::create([
            'pid' => 0,
            'snowflake_id' => 857639559029133313,
            'name' => '超级管理员组',
            'code' => 'SuperAdmin',
            'rules' => '*',
            'status' => Status::Normal->value,
        ]);

        AuthGroupRelation::create([
            'admin_id' => 1,
            'group_id' => 1,
        ]);
    }
}
