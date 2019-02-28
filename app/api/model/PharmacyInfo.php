<?php
namespace app\api\model;

use think\Model;

class PharmacyInfo extends Model
{
    protected $table = 'yb_pharmacy_info';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    /*
     * 药店详情信息
     */
    public static function getDetail($id)
    {

        return self::alias('info')
            ->where(['info.id'=>$id])
            ->field('info.id,pharmacy_name,pharmacy_code,start_store_time,end_store_time,pharmacy_address,pharmacy_lat,pharmacy_lng,pharmacy_store_pic,pharmacy_ismed,pharmacy_isdoor,pharmacy_phone,active.val')
            ->join(['yb_pharmacy_activity'=>'active'],'info.id = active.sid','LEFT')
            ->find()->toArray();
    }
}