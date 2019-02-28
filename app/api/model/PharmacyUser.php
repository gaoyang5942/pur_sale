<?php
namespace app\api\model;

use think\Model;

class PharmacyUser extends Model
{
    protected $table = 'yb_pharmacy_user_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    public function order()
    {
    	return $this->hasMany('Order', 'uid');
    }
}