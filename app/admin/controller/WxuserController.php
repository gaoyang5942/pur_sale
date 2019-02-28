<?php
namespace app\admin\controller;

use base\controller\AdminBaseController;
use think\Db;

/**药店用户管理
 *@author lucy
 *@time 2018-09-06
 */

class WxuserController extends AdminBaseController
{
    /**
     *用户列表
     */
    public function index()
    {

        $state    = $this->request->param("state");
        $keywords = $this->request->param("keywords");
        $where = "1=1";

        if (!empty($keywords)) {
            $where .= " and (";
            $where .= " userinfo_username like '%" . $keywords . "%'";
            $where .= " or userinfo_phone like '%" . $keywords . "%'";
            $where .= " or userinfo_card_id like '%" . $keywords . "%'";
            $where .= ")";
        }

        $list = Db::name("pharmacy_user_info")
            ->where($where)
            ->order("id desc")
            ->paginate(15)
            ->each(function ($item, $key) {
                $item['state_text'] = $item['userinfo_state'] == 1 ? '启用' : '禁用';
                return $item;
            });

        $list->appends(['keywords' => $keywords]);
        // 获取分页显示
        $page = $list->render();
        $this->assign("page", $page);
        $this->assign("list", $list);
        $this->assign('edit_url', url('wxuser/editstate'));
        return $this->fetch();
    }

    /**
     *修改用户状态
     */
    public function editstate()
    {

        $id    = $this->request->param('id', 0, 'intval');
        $state = $this->request->param("state", 0);
        $ret   = ['error' => 1, 'msg' => ''];

        if (!empty($id)) {
            $state  = $state == 0 ? 1 : 0;
            $result = Db::name("pharmacy_user_info")->where("id", $id)->update(["userinfo_state" => $state]);
            if ($result !== false) {
                $ret['error'] = 0;
                //写日志
                service('log', 'insert', ['2', '编辑用户状态', session('admin_id'), serialize($this->request->param())]);
            } else {
                $ret['msg'] = "用户状态改变失败";
            }
            return json_encode($ret);

        }
    }
    /**
     *用户收货地址列表
     */
    public function address()
    {
        $id = $this->request->param('id', '', 'intval');

        $list     = Db::name("user_address_info")->where("uid", $id)->order("id desc")->select()->toArray();
        $province = array_column($list, 'pid');
        $city     = array_column($list, 'cid');
        $area     = array_column($list, 'did');

        $province_collect = Db::name('city_province')->whereIn('id', array_unique($province))->column('name', 'id');
        $city_collect     = Db::name('city_city')->whereIn('id', array_unique($city))->column('name', 'id');
        $area_collect     = Db::name('city_district')->whereIn('id', array_unique($area))->column('name', 'id');

        $this->assign([
            "list"     => $list,
            'province' => $province_collect,
            'city'     => $city_collect,
            'area'     => $area_collect,
        ]);
        return $this->fetch();
    }
}
