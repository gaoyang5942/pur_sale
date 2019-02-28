<?php
namespace app\admin\controller;

use base\controller\AdminBaseController;
use think\Db;

/**
 * Class UserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '管理组',
 *     'action' => 'default',
 *     'parent' => 'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '管理组'
 * )
 */
class UserController extends AdminBaseController
{

    /**
     * 管理员列表
     */
    public function index()
    {
        $where = ["user_type" => 1];
        /**搜索条件**/
        $userLogin = $this->request->param('user_login');

        if ($userLogin) {
            $where['user_login'] = ['like', "%$userLogin%"];
        }

        $users = Db::name('user')->where($where)->order("id DESC")->paginate(10);
        $users->appends(['user_login' => $userLogin]);
        // 获取分页显示
        $page     = $users->render();
        $rolesSrc = Db::name('role')->select();
        $roles    = [];
        foreach ($rolesSrc as $r) {
            $roleId           = $r['id'];
            $roles["$roleId"] = $r;
        }
        $this->assign("page", $page);
        $this->assign("roles", $roles);
        $this->assign("users", $users);
        return $this->fetch();
    }

    /**
     * 管理员添加
     */
    public function add()
    {
        $roles = Db::name('role')->where(['status' => 1])->order("id DESC")->select();
        //药店品牌搜索
        $brand = Db::name('pharmacy_brand_info')->where(['isvalid' => 1])->select();
        //药店搜索
        $store = Db::name('pharmacy_info')->where(['isvalid' => 1])->select();
        $this->assign('brand', $brand);
        $this->assign('store', $store);
        $this->assign("roles", $roles);
        return $this->fetch();
    }

    /**
     * 管理员添加提交
     */
    public function add_post()
    {
        if ($this->request->isPost()) {
            $request = ($this->request->param());
            //var_dump($request);die;
            if (!empty($request['role_id']) && is_array($request['role_id'])) {
                $result = $this->validate($request, 'User');
                if ($result !== true) {
                    $this->error($result);
                } else {
                    if ($request['role_id'][0] == 4) {
                        $data['pid']  = $request['pid'];
                        $data['spid'] = '0';
                    } elseif ($request['role_id'][0] == 9 || $request['role_id'][0] == 10) {
                        if ($request['spid'] == "" && $request['role_id'][0] == 9) {
                            $this->error("必须选择连锁店！");
                        }
                        $data['pid']  = $request['pid'];
                        $data['spid'] = $request['spid'];
                    }
                    $data['user_login']    = $request['user_login'];
                    $data['user_pass']     = password($request['user_pass']);
                    $data['last_login_ip'] = get_client_ip();
                    $data['create_time']   = time();
                    $result                = DB::name('user')->insertGetId($data);

                    if ($result !== false) {
                        if (get_current_admin_id() != 1 && $request['role_id'][0] == 1) {
                            $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                        }
                        Db::name('RoleUser')->insert(["role_id" => $request['role_id'][0], "user_id" => $result]);
                        //写日志
                        service('log', 'insert', ['2', '添加管理员', session('admin_id'), serialize($this->request->param())]);
                        $this->success("添加成功！", url("user/index"));
                    } else {
                        $this->error("添加失败！");
                    }
                }
            } else {
                $this->error("请为此用户指定角色！");
            }
        }
    }

    /**
     * 管理员编辑
     */
    public function edit()
    {
        $id       = $this->request->param('id', 0, 'intval');
        $roles    = DB::name('role')->where(['status' => 1])->order("id DESC")->select();
        $role_ids = DB::name('RoleUser')->where(["user_id" => $id])->column("role_id");
        $user     = DB::name('user')->where(["id" => $id])->find();
        //药店品牌搜索
        $brand = Db::name('pharmacy_brand_info')->where(['isvalid' => 1])->select();
        //药店搜索
        $store = Db::name('pharmacy_info')->where(['isvalid' => 1, 'pid' => $user['pid']])->select();
        $this->assign("roles", $roles);
        $this->assign("role_ids", $role_ids);
        $this->assign("user", $user);
        $this->assign('brand', $brand);
        $this->assign('store', $store);
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 管理员编辑提交
     * @adminMenu(
     *     'name'   => '管理员编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员编辑提交',
     *     'param'  => ''
     * )
     */
    public function edit_post()
    {
        if ($this->request->isPost()) {
            $request = $this->request->param();
            if (!empty($request['role_id']) && is_array($request['role_id'])) {
                $result = $this->validate($this->request->param(), 'User.edit');

                if ($result !== true) {
                    // 验证失败 输出错误信息
                    $this->error($result);
                } else {
                    if ($request['role_id'][0] == 4) {
                        $data['pid']  = $request['pid'];
                        $data['spid'] = '0';
                    } elseif ($request['role_id'][0] == 9 || $request['role_id'][0] == 10) {
                        if ($request['spid'] == "" && $request['role_id'][0] == 9) {
                            $this->error("必须选择连锁店！");
                        }
                        $data['pid']  = $request['pid'];
                        $data['spid'] = $request['spid'];
                    }
                    $data['user_login'] = $request['user_login'];
                    if (!empty($request['user_pass'])) {
                        $data['user_pass'] = password($request['user_pass']);
                    }
                    $data['last_login_ip'] = get_client_ip();
                    $result                = DB::name('user')->where("id", $request['id'])->update($data);
                    if ($result !== false) {
                        DB::name("RoleUser")->where("user_id", $request['id'])->delete();
                        if (get_current_admin_id() != 1 && $request['role_id'][0] == 1) {
                            $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                        }
                        DB::name("RoleUser")->insert(["role_id" => $request['role_id'][0], "user_id" => $request['id']]);
                        //写日志
                        service('log', 'insert', ['2', '编辑管理员', session('admin_id'), serialize($this->request->param())]);
                        $this->success("保存成功！");
                    } else {
                        $this->error("保存失败！");
                    }
                }
            } else {
                $this->error("请为此用户指定角色！");
            }

        }
    }

    /**
     * 管理员个人信息修改
     * @adminMenu(
     *     'name'   => '个人信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改',
     *     'param'  => ''
     * )
     */
    public function user_info()
    {
        $id   = get_current_admin_id();
        $user = Db::name('user')->where(["id" => $id])->find();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 管理员个人信息修改提交
     * @adminMenu(
     *     'name'   => '管理员个人信息修改提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改提交',
     *     'param'  => ''
     * )
     */
    public function user_info_post()
    {
        if ($this->request->isPost()) {

            $data             = $this->request->post();
            $data['birthday'] = strtotime($data['birthday']);
            $data['id']       = get_current_admin_id();
            $create_result    = Db::name('user')->update($data);
            if ($create_result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 管理员删除
     * @adminMenu(
     *     'name'   => '管理员删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id == 1) {
            $this->error("最高管理员不能删除！");
        }

        if (Db::name('user')->delete($id) !== false) {
            Db::name("RoleUser")->where(["user_id" => $id])->delete();
            //写日志
            service('log', 'insert', ['2', '删除管理员', session('admin_id'), serialize($this->request->param())]);
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 停用管理员
     * @adminMenu(
     *     'name'   => '停用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '停用管理员',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('user')->where(["id" => $id, "user_type" => 1])->setField('user_status', '0');
            if ($result !== false) {
                //写日志
                service('log', 'insert', ['2', '禁用管理员', session('admin_id'), serialize($this->request->param())]);
                $this->success("管理员停用成功！", url("user/index"));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 启用管理员
     * @adminMenu(
     *     'name'   => '启用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '启用管理员',
     *     'param'  => ''
     * )
     */
    public function cancel_ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('user')->where(["id" => $id, "user_type" => 1])->setField('user_status', '1');
            if ($result !== false) {
                //写日志
                service('log', 'insert', ['2', '启用管理员', session('admin_id'), serialize($this->request->param())]);
                $this->success("管理员启用成功！", url("user/index"));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 品牌和药店二级联动
     */
    public function get_brand_store()
    {
        $brand = $this->request->param('brand');
        if ($brand == "9999") {
            $data = Db::name('pharmacy_info')->where(["pid" => 0])->select();
        } else {
            $data = Db::name('pharmacy_info')->where(["pid" => $brand])->select();
        }
        return $data;
    }

}
