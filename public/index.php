<?php

// +----------------------------------------------------------------------
// | THINKPHP [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.THINKPHP.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------
// [ 入口文件 ]

// 调试模式开关
define("APP_DEBUG", true);

// 定义CMF根目录,可更改此目录
define('BASE_ROOT', __DIR__ . '/../');

// 定义应用目录
define('APP_PATH', BASE_ROOT . 'app/');

// 定义CMF核心包目录
define('BASE_PATH', BASE_ROOT . 'think/base/');

// 定义扩展目录
define('EXTEND_PATH', BASE_ROOT . 'think/extend/');
define('VENDOR_PATH', BASE_ROOT . 'think/vendor/');

//定义配置目录
define('CONF_PATH', BASE_ROOT .'config/');

// 定义应用的运行时目录
define('RUNTIME_PATH', BASE_ROOT . 'runtime/');


// 加载框架基础文件
require BASE_ROOT . 'think/thinkphp/base.php';

// 执行应用
\think\App::run()->send();

//test hooks1111222222
