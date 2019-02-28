<?php
namespace app\service\service;

use think\Db;

/**
 * 后台菜单管理serivce 包含后台菜单的所有公共方法
 */
class MenuService
{
    /**
     * 制作后台菜单  输入后台菜单数组 输出制作完成的结果
     * @param  array  $menu
     * @return string $data
     */
    public static function create_menu_html($menu)
    {
        $data = SELF::getsubmenu($menu);
        return $data;
    }

    //制作主菜单
    public static function getsubmenu($submenus)
    {
        $html = '';
        foreach ($submenus as $menu) {
            $html .= '<li>';
            if (empty($menu['items'])) {
                $html .= '<a href="javascript:openapp(\'' . $menu["url"] . '\',\'' . $menu["id"] . '\',\'' . $menu["name"] . '\',true);"><i class="fa fa-' . ($menu['icon'] ? $menu['icon'] : "desktop") . '"></i><span class="menu-text"> ' . $menu['name'] . ' </span></a>';
            } else {
                $data = SELF::getsubmenu1($menu['items']);
                $html .= '<a href="#" class="dropdown-toggle"><i class="fa fa-' . ($menu['icon'] ? $menu['icon'] : "desktop") . ' normal"></i><span class="menu-text normal"> ' . $menu["name"] . ' </span><b class="arrow fa fa-angle-right normal"></b><i class="fa fa-reply back"></i><span class="menu-text back">返回</span></a><ul class="submenu">' . $data . '</ul>';
            }
            $html .= '</li>';
        }
        $html .= '';

        return $html;
    }

    //制作1级菜单
    public static function getsubmenu1($submenus)
    {
        $html = '';
        $user = Db::name('user')->where('id', session('admin_id'))->find();
        foreach ($submenus as $menu) {
            if ($user['pid'] == '0' && $user['spid'] == '0' && ($menu['name'] == '本店药品类别' || $menu['name'] == '本店药品管理')) {
                continue;
            }
            $html .= '<li>';
            if (empty($menu['items'])) {
                $html .= '<a href="javascript:openapp(\'' . $menu["url"] . '\',\'' . $menu["id"] . '\',\'' . $menu["name"] . '\',true);"><i class="fa fa-caret-right"></i><span class="menu-text"> ' . $menu["name"] . ' </span></a>';
            } else {
                $data = SELF::getsubmenu2($menu['items']);
                $html .= '<a href="#" class="dropdown-toggle"><i class="fa fa-caret-right"></i><span class="menu-text">' . $menu["name"] . '</span><b class="arrow fa fa-angle-right"></b></a><ul class="submenu">' . $data . '</ul>';
            }
            $html .= '</li>';
        }

        return $html;
    }

    //制作2级菜单
    public static function getsubmenu2($submenus)
    {
        $html = '';
        foreach ($submenus as $menu) {
            $html .= '<li>';
            $html .= '<a href="javascript:openapp(\'' . $menu["url"] . '\',\'' . $menu["id"] . '\',\'' . $menu["name"] . '\',true);">&nbsp;<i class="fa fa-angle-double-right"></i><span class="menu-text"> ' . $menu["name"] . ' </span>';
            $html .= '</li>';
        }
        return $html;
    }

}
