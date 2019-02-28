<?php
namespace app\api\controller;
use think\Exception;
use think\Db;
use think\Request;
use base\controller\HomeBaseController;
use app\api\model\Order;
use app\api\model\PharmacyInfo;

class CourierController extends HomeBaseController{

    public function order(){
        $param = $this->request->param();
        $num   = $param['drugorder_number'];
        $order = Order::with('orderproducts.detail')->where(['drugorder_number'=>$num])->find();
        $order['info'] = PharmacyInfo::where(['id'=>$order['pid']])
        					->field('pharmacy_name,pharmacy_phone,province,city,district,pharmacy_address')
        					->find();
        $order['city'] = Db::name('city_city')->where(['id'=>$order['info']['city']])->value('name');
        $order['dis']  = Db::name('city_district')->where(['id'=>$order['info']['district']])->value('name');
        $order['pro']  = Db::name('city_province')->where(['id'=>$order['info']['province']])->value('name');
        $order['addr'] = Db::name('user_address_info')->where(['id'=>$order['address_id']])->value('address_detailed');
		jsonResponse('200','获取数据成功',$order);
    }

    public function save(){
    	$param = $this->request->param();
    	if (!$param['drugorder_number']) {
    		jsonResponse('400','操作失败');
    	}
    	Order::where(['drugorder_number'=>$param['drugorder_number']])->update(['drugorder_status'=>10]);
    	jsonResponse('200','操作成功');
    }
}
