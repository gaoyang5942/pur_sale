<?php
namespace app\wechat\controller;

use app\api\controller\ApiBaseController;
use app\api\model\PharmacyDrugStore;
use app\api\model\PharmacyInfo;

class PharmacyController extends ApiBaseController {

	public function _initialize(){

	}
	//药品列表选购页面
	public function index(){

        return $this->fetch();

    }

}
