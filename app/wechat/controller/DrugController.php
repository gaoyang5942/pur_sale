<?php
namespace app\wechat\controller;
use think\Db;
use think\Request;
use app\api\model\PharmacyDrugDetailed;
use app\api\controller\ApiBaseController;

class DrugController extends ApiBaseController {

	/**
	 *	药品详细
	 */
	public function index() {
		
		return $this->fetch();
	}

}
