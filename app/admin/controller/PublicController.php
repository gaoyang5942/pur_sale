<?php
namespace app\admin\controller;

use base\controller\AdminBaseController;
use think\Db;

class PublicController extends AdminBaseController{
    public function _initialize(){
    }

    /**
     * 后台登陆界面
     */
    public function login(){
        if (empty(session("admin_login_flag"))) {
            //$this->error('非法登录!', get_root() . '/');
            return redirect(get_root() . "/");
        }

        $admin_id = session('admin_id');
        if (!empty($admin_id)) {//已经登录
            return redirect(url("admin/Index/index"));
        } else {
            return $this->fetch();
        }
    }

    /**
     * 登录验证
     */
    public function do_login(){
        if (empty(session("admin_login_flag"))) {
            $this->error('非法登录!', get_root() . '/');
        }

        $captcha = $this->request->param('captcha');
        if (empty($captcha)) {
            $this->error("验证码不能为空！");
        }
        //验证码
        if (!captcha_check($captcha)) {
            $this->error("验证码错误！");
        }

        $name = $this->request->param("username");
        if (empty($name)) {
            $this->error("用户名或邮箱不能为空！");
        }
        $pass = $this->request->param("password");
        if (empty($pass)) {
            $this->error("密码不能为空！");
        }
        if (strpos($name, "@") > 0) {//邮箱登陆
            $where['user_email'] = $name;
        } else {
            $where['user_login'] = $name;
        }

        $result = Db::name('user')->where($where)->find();

        if (!empty($result) && $result['user_type'] == 1) {
            if (compare_password($pass, $result['user_pass'])) {
                $groups = Db::name('RoleUser')
                    ->alias("a")
                    ->join('__ROLE__ b', 'a.role_id =b.id')
                    ->where(["user_id" => $result["id"], "status" => 1])
                    ->value("role_id");
                if ($result["id"] != 1 && (empty($groups) || empty($result['user_status']))) {
                    $this->error("用户已禁用！");
                }
                //登入成功页面跳转
                session('admin_id', $result["id"]);
                session('name', $result["user_login"]);
                $result['last_login_ip']   = get_client_ip(0, true);
                $result['last_login_time'] = time();
                $token                     = generate_user_token($result["id"], 'web');
                if (!empty($token)) {
                    session('token', $token);
                }
                Db::name('user')->update($result);
                Db::name('user')->where($where)->setInc("login_count");
                cookie("admin_username", $name, 3600 * 24 * 30);
                session("admin_login_flag", null);
                session("admin_login_flag", null);

                //写日志
                $log_service = \think\Loader::model('service/log','service');
                $log_service->insert(1,"登录",$result["id"]);

                $this->success("登录成功！", url("admin/Index/index"));
            } else {
                $this->error("密码不正确！");
            }
        } else {
            $this->error("用户名不存在！");
        }
    }

    /**
     * 后台管理员退出
     */
    public function logout(){
        session('admin_id', null);
        return redirect(url('/admin', [], false, true));
    }
}
