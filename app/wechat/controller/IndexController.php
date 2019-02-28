<?php
namespace app\wechat\controller;

use base\controller\HomeBaseController;

class IndexController extends HomeBaseController{
	public function index(){
		return $this->fetch();
	}
	//收货地址列表  user_address_info
	public function address_info(){
		return $this->fetch();
	}
	//新增收货地址
	public function add_address(){
		return $this->fetch();
	}
	//搜索页面
	public function search(){
		return $this->fetch();
	}
	//购药中心
	public function medicine(){
		
		return $this->fetch();
	}
	//扫一扫
	public function wescan(){
		return $this->fetch();
	}
	//配送员
	public function courier(){
		
		return view('courier/index');
	}
	//配送员订单
	public function order(){
		
		return view('courier/order');
	}
}
