<?php
namespace app\service\service;

use think\Db;
use think\Request;

class LogService{
    /**
     * 存储日志
     * @param type(日志类型 1登录 2操作 3缴费 4对账)
     * @param description 日志描述
     * @param uid (type1 2时为管理员id type3时为缴费用户id)
     * @param group 分组
     * @param controller 控制器
     * @param function 方法名
     * @param data 数据  序列化后存储
     * @return bool true为成功 false为失败
     */
    public function insert($type, $description, $uid, $data=''){
        $data = [
                    'type'        => $type,
                    'description' => $description,
                    'uid'         => $uid,
                    'module'      => Request::instance()->module(),
                    'controller'  => Request::instance()->controller(),
                    'action'      => Request::instance()->action(),
                    'data'        => $data,
                    'create_time' => time()
                ];
        if(Db::name('system_log')->insert($data)){
            return true;
        }else{
            return false;
        }
    }
}
