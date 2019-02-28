<?php
namespace app\api\model;

use think\Model;

class OrderStatus extends Model
{
    protected $table = 'yb_order_status_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    // 生成订单状态值
    const CREATE = 0;//下单成功
    const WAIT_PAY = 5;//待支付
    const PAYED = 6;//支付完成
    const SHIPPING = 3;//配送中
    const REFUNDING = 7;//退款中
    const REFUNDED = 8;//退款完成
    const WAIT_SHIPPING = 9;//货到付款

    protected $hidden = [

    ];

}