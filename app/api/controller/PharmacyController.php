<?php
namespace app\api\controller;
use app\api\model\PharmacyDrugStore;
use app\api\model\PharmacyInfo;
use app\api\model\PharmacyRadio;
use app\api\Wechat;

class PharmacyController extends ApiBaseController {

	/**
	 * 药店药品列表
	 */
	public function druglist() {

        $store_id  = $this->request->param('store_id');
        if(empty($store_id)){
            jsonResponse('400','参数错误',[]);
        }
        //药品列表
        $drug_list = PharmacyDrugStore::getList($store_id);

        jsonResponse('200','',$drug_list);
	}

	/*
	 * 药店头部信息
	 */
	public function info()
    {

        $store_id  = $this->request->param('store_id');
        if(empty($store_id)){
            jsonResponse('400','参数错误',[]);
        }
        //药店头部信息
        $pharmacy_info = PharmacyInfo::getDetail($store_id);
        jsonResponse('200','',$pharmacy_info);
    }

    /*
	 * 药店广播消息
	 */
    public function radio()
    {
        $store_id  = $this->request->param('store_id');
        if(empty($store_id)){
            jsonResponse('400','参数错误',[]);
        }
        //药店广播
        $radio_list = PharmacyRadio::getList($store_id);

        jsonResponse('200','',$radio_list);
    }

    /*
     * 药品搜索
     */
    public function search(){
        $search = $this->request->param('search');
        $sid    = $this->request->param('sid');

        $data   = PharmacyDrugStore::search($search,$sid);
        
        jsonResponse('200','',$data);
    }

    /*
     * 药品库存
     */
    public function num(){
        $id = $this->request->param('id');
        $num = PharmacyDrugStore::where(['id' => $id])->value('drug_store_num');
        jsonResponse('200','',$num);
    }
}
