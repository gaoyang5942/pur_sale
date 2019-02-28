<?php
namespace app\api\controller;
use think\Db;
use think\Request;

//医保调用药店接口类
class MedicalController extends ApiBaseController {

	/**
     * 根据药品编码获取药品名称和规格
     */
    public function get_ns()
    {
    	$code = $this->request->param('code');
    	$drug = Db::name('pharmacy_drug_detailed')->where(['drug_detailed_code' => $code, 'isvalid' => 1])->find();
        if ($result['druginfo'] == ''){
            return json_encode(['data' => $drug, 'msg' => 'error', 'code' => '0'], 320);
        }else{
            return json_encode(['data' => $drug, 'msg' => 'success', 'code' => '1'], 320);
        }
    }

}
