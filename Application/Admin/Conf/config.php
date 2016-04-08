<?php
return array(
	//'配置项'=>'配置值'
    'VAR_PAGE'=>'pageNum',
    'SITE_NAME'=>'滴滴保健管理后台',

    'USER_AUTH_ON' => true, //是否需要验证权限的开关
    'LAYOUT_ON' =>  true, // 是否启用布局
    'USER_AUTH_TYPE' => 1, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'ADMIN_AUTH_KEY' => 'administrator',
    'DEFAULT_CONTROLLER'=>'Public',
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
    'USER_AUTH_GATEWAY' => "/".MODULE_NAME.'/Public/login', // 默认认证网关
    'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'GUEST_AUTH_MODULE' => '', // 游客的用户可访问的模块
    'DEFAULT_LANG'=>  'zh-cn', // 默认语言
    'LANG_AUTO_DETECT' => true, //关闭语言的自动检测，如果你是多语言可以开启
    'LANG_SWITCH_ON' => true, //开启语言包功能，这个必须开启
    'SESSION_PREFIX'=>'admin',
);