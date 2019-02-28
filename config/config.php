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
    //微信配置
    'APPID'            => 'wxec9bb751b2b6e61a',
    'APPID_Z'          => 'wxc927414494bb2d19',
    'APPSECRET'        => '0885e0b24f1e192dcb402ff3ef3f2484',
    'AESKey'           => 'wUvmWJCa3LS0eGloeLeFfgJYS493EjmlYESSDAEuSm2',
    'PAYINSURANCE_URL' => 'https://api.weixin.qq.com/payinsurance',//医保支付url地址
    'YB_PAY_KEY'       => '75a455fa7edb0747579f7be0b55cb340',//商户平台设置的密钥key
    'AGENT_APPID'      => 'wx09150f3513d6d05c',//代理公众号的appid
    'AGENT_SECRET'     => '02de467c9e45c4010fc65145902cb9e6',//代理公众号的appsecret
    'MCHID'            => '1371623402',//微信支付分配的商户号
    'AGENT_SUB_MCHID'  => '1495987252',
    'ENC_KEY'          => 'ENCODE_STRING',//加密解密key
    'WXBK_KEY'         => 'ud1892083f378kadyfd9207bu9e87t8ac',//绑卡授权key
    //分页配置
    'paginate'      => [
        'type'      => '\base\paginator\Bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'baidu_map_key'     =>  'G5E4bjoHG8YgqG6352yhhyu5hUWFGFGg',
    'qq_map_key'        =>  'SDNBZ-7FCRU-K3WVX-4SB33-PKAXV-3FB7L',
];
return $configs;
