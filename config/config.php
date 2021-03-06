<?php

$configs = [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 应用命名空间
    'app_namespace'           => 'app',
    // 应用模式状态
    'app_status'              => APP_DEBUG ? 'debug' : 'release',
    // 注册的根命名空间
    'root_namespace'          => ['base' => BASE_PATH],
    // 扩展函数文件
    'extra_file_list'         => [BASE_PATH . 'common' . EXT, THINK_PATH . 'helper' . EXT],
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'          => 'htmlspecialchars',
    // 应用类库后缀
    'class_suffix'            => true,
    // 控制器类后缀
    'controller_suffix'       => true,
    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------
    // 默认模块名
    'default_module'          => 'wechat',
    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    // 路由配置文件（支持配置多个）
    'route_config_file'       => ['route'],
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    'template'                => [
        // 标签库标签开始标记
        'taglib_begin'    => '<',
        // 标签库标签结束标记
        'taglib_end'      => '>',
        'taglib_build_in' => 'base\lib\Basetag,cx',
        'tpl_cache'       => APP_DEBUG ? false : true,
        'tpl_deny_php'    => false
    ],
    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    'cache'  => [
        'type'   => 'File',
        'path'   => CACHE_PATH,
        'prefix' => '',
        'expire' => 0,
    ],
    // +----------------------------------------------------------------------
    // | 数据库设置
    // +----------------------------------------------------------------------
    'database'            => [
        // 数据集返回类型
        'resultset_type'  => 'collection',
    ],
    'ENC_KEY'          => 'ENCODE_STRING',//加密解密key
    //分页配置
    'paginate'      => [
        'type'      => '\base\paginator\Bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'baidu_map_key'     =>  '',
    'qq_map_key'        =>  '',
];
return $configs;
