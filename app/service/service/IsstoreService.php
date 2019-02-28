<?php
namespace app\service\service;

use think\Db;
use think\Request;
/**
 * 判断是否是总店服务
 */
class IsstoreService{
    /**
     * 判断是否是总店
     * $id 后台登录id 用get_current_admin_id()获取
     */
    public function isstore($id){
        $user = Db::name('user')->where(['id'=>$id])->find();
        if($user['pid'] != '0' && $user['spid'] == '0'){
            //总店（品牌店）
            return true;
        }else{
            //连锁店
            return false;
        }
    }
}
