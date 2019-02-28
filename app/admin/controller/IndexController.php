<?php
namespace app\admin\controller;

use base\controller\AdminBaseController;
use think\Db;
use app\admin\model\AdminMenuModel;

class IndexController extends AdminBaseController{

    public function _initialize(){
        //检查设置
        $adminSettings = get_option('admin_settings');
        if (empty($adminSettings['admin_password']) || $this->request->path() == $adminSettings['admin_password']) {

            $adminId = get_current_admin_id();
            if (empty($adminId)) {
                session("admin_login_flag", 1);
            }
        }
        parent::_initialize();
    }

    /**
     * 后台首页
     */
    public function index(){

        //取后台菜单并存储到缓存中
        $adminMenuModel = new AdminMenuModel();
        $menus          = cache('admin_menus_' . get_current_admin_id(), '', null, 'admin_menus');
        if (empty($menus)) {
            $menus = $adminMenuModel->menuTree();
            cache('admin_menus_' . get_current_admin_id(), $menus, null, 'admin_menus');
        }
        //调用公共方法生成后台菜单html
        $menus = service('menu','create_menu_html',[$menus]);
        $this->assign("menus", $menus);
        return $this->fetch();
    }

}
