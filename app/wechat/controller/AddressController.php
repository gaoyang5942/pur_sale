<?php
namespace app\wechat\controller;
use think\Exception;
use think\Db;
use think\Request;
use base\controller\HomeBaseController;

/**收货地址管理
 *@author lucy
 *@time 2018-09-26
 **/

class AddressController extends HomeBaseController{
    /**
	 *用户收货地址列表
	 */
	public function index(){
		return $this->fetch();
		
	}

    /**
	 *新增用户收货地址
	 */
	public function add_address(){
		
		return $this->fetch();
	}

	/**
	 *编辑用户收货地址
	 */
	public function edit_address(){
        
		return $this->fetch();		

	}

}