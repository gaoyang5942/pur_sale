<?php
namespace app\api\controller;
use think\Db;
use think\Request;
use app\api\model\PharmacyDrugDetailed;
use app\api\model\PharmacyInfo;

class DrugController extends ApiBaseController {

	/**
	 * 药品详细
	 * id  药品ID
	 */
	public function index() {
		$id  = $this->request->param('id');
		$pid = $this->request->param('pid');
		$where['id'] = $id;
		$where['isvalid'] = 1;
		//查询药品相关数据
		$data  = PharmacyDrugDetailed::with('store,drug')->where($where)->find();
		//药店名称
		$name  = PharmacyInfo::where(['id'=>$pid])->field('pharmacy_name')->find();
		//查询药品销量
		$count = Db::name('pharmacy_drug_order')->alias('o')
					->join('drug_store_detailed s','o.drugorder_number = s.drugstore_number','LEFT')
					->where(['o.drugorder_status'=>'6','s.drugstore_code'=>$id])
					->sum('s.drugstore_num');
		$data['name']  = $name['pharmacy_name'];
		$data['count'] = $count;
		return json_encode(['data' => $data, 'msg' => 'success', 'code' => '200'], 320);
	}

}
