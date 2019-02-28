<?php
namespace app\api\model;

use think\Model;

class PharmacyRadio extends Model
{
    protected $table = 'yb_pharmacy_radio';


    protected $hidden = [

    ];

    /*
     * 获取广播列表
     */
    public static function getList($sid)
    {
        return self::where(['sid' => $sid, 'isvalid' => 1])
            ->field('sid,content,create_time')
            ->select()
            ->toArray();
    }

}