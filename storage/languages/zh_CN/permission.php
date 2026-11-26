<?php

declare(strict_types=1);

return [
    'rule'  => [
        'idRequired'              => '缺少ID参数',
        'pidRequired'             => '请选择上级权限',
        'typeRequired'            => '请选择权限类型',
        'typeIn'                  => '权限类型错误',
        'nameRequired'            => '请填写菜单名称',
        'nameUnique'              => '菜单名称已存在',
        'routeNameRequiredIf'     => '请填写路由名称',
        'routePathRequiredUnless' => '请填写路由路径',
        'componentRequiredIf'     => '请填写组件路径',
        'permRequiredIf'          => '请填写权限标识',
        'iconRequiredUnless'      => '请填写权限图标',
        'weightRequired'          => '请填写权重',
        'statusRequired'          => '请选择权限状态',
    ],
    'group' => [
        'idGt'           => '不可操作超级管理员组',
        'pidRequired'    => '请选择父级权限分组',
        'pidExists'      => '父级权限分组不存在',
        'nameRequired'   => '请填写权限分组名称',
        'rulesRequired'  => '请选择权限分组权限',
        'rulesRegex'     => '权限分组格式错误',
        'statusRequired' => '请选择权限分组状态',
    ],
    'admin' => [
        'idGt'             => '不可操作超级管理员',
        'groupIdsRequired' => '请选择权限分组',
        'usernameRequired' => '请填写用户名',
        'usernameRegex'    => '用户名必须6-30个字符',
        'usernameUnique'   => '用户名已存在',
        'nicknameRequired' => '请填写管理员昵称',
        'mobileRegex'      => '手机号码格式错误',
        'mobileUnique'     => '手机号码已存在',
        'emailRequired'    => '请填写邮箱',
        'emailEmail'       => '邮箱格式错误',
        'emailUnique'      => '邮箱已存在',
        'passwordRequired' => '请填写密码',
        'statusRequired'   => '请选择管理员状态',
    ],
    'login' => [
        'accountDisable'   => '账号被禁用',
        'accountNotExists' => '账号不存在',
        'passwordError'    => '密码错误',
    ],

];
