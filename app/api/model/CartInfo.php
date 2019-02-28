<?php
namespace app\api\model;

use think\Model;

class CartInfo extends Model
{
    protected $table = 'yb_cart_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    public function drug()
    {
        return $this->hasOne('app\api\model\PharmacyDrug', 'id', 'did');
    }
}