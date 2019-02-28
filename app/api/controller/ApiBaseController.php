<?php
namespace app\api\controller;

use base\controller\HomeBaseController;
use app\api\model\PharmacyUser;
use think\Request;

class ApiBaseController extends HomeBaseController
{
    protected $user_info = [];

    public function _initialize()
    {
        // session('uid', 2);
        $controller = 'app\api\controller\\' . request()->controller() . 'Controller';
        $action     = request()->action();
        $class      = new \ReflectionClass($controller);
        $need_auth  = $class->getStaticPropertyValue('need_auth', []);

        if (in_array($action, $need_auth)) {
            $user_info = [];
            $uid = session('uid');
            if ($uid) {
                $user_info = PharmacyUser::get(function ($query) use ($uid){
                    $query->where('isvalid', 1)->where('id', $uid);
                });
            }

            if (empty($user_info)) {
                echo json_encode(['code' => 501, 'msg' => 'no_auth'], 320);
                die();
            }

            $this->user_info = $user_info;
        }
    }
}
