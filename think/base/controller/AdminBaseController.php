<?php
// +----------------------------------------------------------------------
// | THINKPHP [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.THINKPHP.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace base\controller;

use think\Db;

class AdminBaseController extends BaseController
{

    const PAGE_SIZE = 15;//后台统一分页条数

    public function _initialize()
    {
        // 监听admin_init
        hook('admin_init');
        parent::_initialize();
        $session_admin_id = session('admin_id');
        if (!empty($session_admin_id)) {
            $user = Db::name('user')->where(['id' => $session_admin_id])->find();

            if (!$this->checkAccess($session_admin_id)) {
                $this->error("您没有访问权限！");
            }
            $this->assign("admin", $user);
        } else {
            if ($this->request->isPost()) {
                $this->error("您还没有登录！", url("admin/public/login"));
            } else {
                header("Location:" . url("admin/public/login"));
                exit();
            }
        }
    }

    public function _initializeView()
    {
        // $cmfAdminThemePath    = config('cmf_admin_theme_path');
        // $cmfAdminDefaultTheme = get_current_admin_theme();

        // $themePath = "{$cmfAdminThemePath}{$cmfAdminDefaultTheme}";

        // $root = get_root();

        // //使cdn设置生效
        // $cdnSettings = get_option('cdn_settings');
        // if (empty($cdnSettings['cdn_static_root'])) {
        //     $viewReplaceStr = [
        //         '__ROOT__'     => $root,
        //         '__TMPL__'     => "{$root}/{$themePath}",
        //         '__STATIC__'   => "{$root}/static",
        //         '__WEB_ROOT__' => $root
        //     ];
        // } else {
        //     $cdnStaticRoot  = rtrim($cdnSettings['cdn_static_root'], '/');
        //     $viewReplaceStr = [
        //         '__ROOT__'     => $root,
        //         '__TMPL__'     => "{$cdnStaticRoot}/{$themePath}",
        //         '__STATIC__'   => "{$cdnStaticRoot}/static",
        //         '__WEB_ROOT__' => $cdnStaticRoot
        //     ];
        // }

        // $viewReplaceStr = array_merge(config('view_replace_str'), $viewReplaceStr);
        // config('template.view_base', "$themePath/");
        // config('view_replace_str', $viewReplaceStr);
    }

    /**
     * 初始化后台菜单
     */
    public function initMenu()
    {
    }

    /**
     *  检查后台用户访问权限
     * @param int $userId 后台用户id
     * @return boolean 检查通过返回true
     */
    private function checkAccess($userId)
    {
        // 如果用户id是1，则无需判断
        if ($userId == 1) {
            return true;
        }

        $module     = $this->request->module();
        $controller = $this->request->controller();
        $action     = $this->request->action();
        $rule       = $module . $controller . $action;

        $notRequire = ["adminIndexindex", "adminMainindex"];
        if (!in_array($rule, $notRequire)) {
            return auth_check($userId);
        } else {
            return true;
        }
    }

}
