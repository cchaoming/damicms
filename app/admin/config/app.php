<?php
return [
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2,        // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId',    // 用户认证SESSION标记
    'USER_CONTENT_KEY' => 'conids',
    'ADMIN_AUTH_KEY' => 'administrator',
    'USER_AUTH_MODEL' => 'admin',    // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5',    // 用户认证密码加密方式
    'USER_AUTH_GATEWAY' => 'Publics/login',    // 默认认证网关
    'NOT_AUTH_MODULE' => 'Publics,Index',        // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '',        // 默认需要认证模块
    'NOT_AUTH_ACTION' => 'verify,clearcache',        // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '',        // 默认需要认证操作
    'GUEST_AUTH_ON' => false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0,     // 游客的用户ID
    'DB_LIKE_FIELDS' => 'title|remark',
    'RBAC_ROLE_TABLE' => 'dami_role',
    'RBAC_USER_TABLE' => 'dami_role_admin',
    'RBAC_ACCESS_TABLE' => 'dami_access',
    'RBAC_NODE_TABLE' => 'dami_node',
    'TOKEN_ON' => false, // 开启令牌验证
    'FIELD_TYPE' => [0 => '单行文本(text)', 1 => '多行文本不支持编辑器(textarea)', 2 => '多行文本支持编辑器(htmleditor)', 3 => '下拉列表菜单(select)', 4 => '单选按钮(radio)', 5 => '多选列表(multselect)', 6 => '多选按钮(checkbox)', 7 => '单文件上传(file)', 8 => '多文件上传(multifile)'],
];
