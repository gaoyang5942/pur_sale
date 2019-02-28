<?php
namespace app\api\model;

use think\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'yb_order_status_history_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

}