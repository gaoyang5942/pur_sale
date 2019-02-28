<?php
namespace app\api\model;

use think\Model;

class UserAddress extends Model
{
    protected $table = 'yb_user_address_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];


    public function getAddress($uid, $id = 0)
    {
        $ret = $this->where('isvalid', 1)
            ->where('uid', $uid);

        $ret = $id ? $ret->where('id', $id) : $ret->where('address_flag', 1);

        return $ret->find();
    }

}