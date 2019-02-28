<?php
namespace app\api\controller;

use think\Exception;
use think\Db;
use think\Request;
use base\controller\HomeBaseController;

class SearchController extends HomeBaseController{
    /**
     * 药品模糊查询_按名字
     */
    public function searchMedicineName(){
 	    $drug_detailed_name = $this->request->param('drug_detailed_name');
        $lat = session('latitude');
        $lon = session('longitude');
        if ($drug_detailed_name) {
            $where['drug_detailed_name'] = ['like', "%$drug_detailed_name%"];
            $where['isvalid'] = 1;
	        $data=Db::name('pharmacy_drug_detailed')->where($where)->order("id ASC")->select()->toArray();
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    $value['drug_detailed_img'] = 'http://'.$_SERVER['SERVER_NAME'].'/upload/'.$value['drug_detailed_img'];
                    $clom[$value['pid']][] = $value;
                }
                foreach ($clom as $ke => $val) {
                    $data_arr = Db::name('pharmacy_info')->where('isvalid',1)->where('id',$ke)->select()->toArray();
                    foreach ($data_arr as $key => $value) {
                        $distance_km = $this->getDistance($lat,$lon,$value['pharmacy_lat'],$value['pharmacy_lng'],2,1);
                        $data_arr[0]['pharmacy_store_pic'] = 'http://'.$_SERVER['SERVER_NAME'].'/upload/'.$value['pharmacy_store_pic'];
                        $data_arr[0]['distance_km'] = $distance_km;
                        $data_arr[0]['discount'] = Db::name('pharmacy_activity')->where('sid',$value['id'])->value('val');
                        //为排序做准备
                        $sort_arr[] = $distance_km;
                        $clom_arr[] = array(
                            'distance_km' => $distance_km,
                            'pah' => $data_arr[0],
                            'pdt' => $val
                        );
                    }
                }
                sort($sort_arr);
                foreach ($sort_arr as $key => $value) {
                    foreach ($clom_arr as $key1 => $value1) {
                        if($value == $value1['distance_km']){
                            $clom_key[] = $value1;
                        }
                    }
                }
            }
        }
        if(empty($data)){
            return json_encode(['data' => [], 'msg' => 'success','code' => '202'],320);
        }else{
            return json_encode(['data' => $clom_key, 'msg' => 'success','code' => '200'],320);
        }
    }
    /**
     * 计算两点地理坐标之间的距离
     * @param Decimal $longitude1 起点经度
     * @param Decimal $latitude1 起点纬度
     * @param Decimal $longitude2 终点经度
     * @param Decimal $latitude2 终点纬度
     * @param Int   $unit    单位 1:米 2:公里
     * @param Int   $decimal  精度 保留小数位数
     * @return Decimal
     */
    public function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){

      $EARTH_RADIUS = 6370.996; // 地球半径系数
      $PI = 3.1415926;

      $radLat1 = $latitude1 * $PI / 180.0;
      $radLat2 = $latitude2 * $PI / 180.0;

      $radLng1 = $longitude1 * $PI / 180.0;
      $radLng2 = $longitude2 * $PI /180.0;

      $a = $radLat1 - $radLat2;
      $b = $radLng1 - $radLng2;

      $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
      $distance = $distance * $EARTH_RADIUS * 1000;

      if($unit==2){
        $distance = $distance / 1000;
      }

      return round($distance, $decimal);

    }
    /**
     * 药品模糊查询_按类别
     */
    public function searchMedicineType(){
        $cid = $this->request->param('cid');
        if ($cid) {
                $where['cid'] = ['like', "%$cid%"];
                $where['isvalid'] = 1;
    	        $data=Db::name('pharmacy_drug_store')->where($where)->order("id ASC")->select();
        }
	    $this->assign('data',$data);
   	    return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 药品模糊查询_已选店搜药
     */
    public function searchMedicineBrandinfo(){
        $drug_store_name = $this->request->param('drug_store_name');
        $cid = $this->request->param('cid');
	    $pid = $this->request->param('pid');
        if ($cid) {
           if ($drug_store_name) {
                $where['drug_store_name'] = ['like', "%$drug_store_name%"];
                $where['isvalid'] = 1;
    	        $data=Db::name('pharmacy_drug_store')->where($where)->order("id ASC")->select();
           }else{
                $where['cid'] = ['like', "%$cid%"];
                $where['isvalid'] = 1;
    	        $data=Db::name('pharmacy_drug_store')->where($where)->order("id ASC")->select();
            }
        }
	    $this->assign('data',$data);
   	    return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 药店模糊查询
     */
    public function searchMedicine(){
 	    $pharmacy_name = $this->request->param('pharmacy_name');
        if ($pharmacy_name) {
            $lat = session('latitude');
            $lon = session('longitude');
            $where['pharmacy_name'] = ['like', "%$pharmacy_name%"];
	        $data=Db::name('pharmacy_info')->where($where)->where('isvalid',1)->order("id ASC")->select()->toArray();
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    $distance_km = $this->getDistance($lat,$lon,$value['pharmacy_lat'],$value['pharmacy_lng'],2,1);
                    $value['distance_km'] = $distance_km;
                    $value['pharmacy_store_pic'] = 'http://'.$_SERVER['SERVER_NAME'].'/upload/'.$value['pharmacy_store_pic'];
                    $value['discount'] = Db::name('pharmacy_activity')->where('sid',$value['id'])->value('val');
                    $data_clom[$key] = $value;
                }
                //按距离远近排序
                array_multisort(array_column($data_clom,'distance_km'),SORT_ASC,$data_clom);
            }
        }
    	if(empty($data_clom)){
            return json_encode(['data' => [], 'msg' => 'success','code' => '202'],320);
        }else{
            return json_encode(['data' => $data_clom, 'msg' => 'success','code' => '200'],320);
        }
    }
}
