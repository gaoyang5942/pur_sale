<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class AreaSelect extends Model{

    public static function getProvince()
    {

        return Db::table('yb_city_province')->where(['status'=>1])->select()->toArray();
    }

    public static function getCity($province_id)
    {
        return Db::table('yb_city_city')->where(['status'=>1,'provinceId'=>$province_id])->select()->toArray();
    }


    public static function getDistrict($city_id)
    {
        return Db::table('yb_city_district')->where(['status'=>1,'cityId'=>$city_id])->select()->toArray();
    }

}
