<?php
namespace app\api\controller;
use think\Exception;
use think\Db;
use think\Request;
use base\controller\HomeBaseController;
use app\service\service\SafedelService;

/**收货地址管理
 *@author lucy
 *@time 2018-09-26
 **/

class AddressController extends HomeBaseController{


	public function add_address_opation(){
		$param = $this->request->param();
		//跟据活得的坐标取得省市区 id
		if($param['lat'] && $param['lng']){
			$location = $param['lat'].','.$param['lng'];
			$url = "https://apis.map.qq.com/ws/geocoder/v1/?location=".$location."&get_poi=1&key=".config('qq_map_key');
			$res = $this->http_request($url);  
			$result = json_decode($res, true);
			$city = $result['result']['address_component'];
			if($city['province']){
				$province = mb_substr($city['province'], 0, 1, 'utf-8');
				$pid = Db::name('city_province')->where('name','like',"%$province%")->value('id');
			}
			if($city['city']){
				$cid = Db::name('city_city')->where('name',$city['city'])->value('id');
			}
			if($city['district']){
				$did = Db::name('city_district')->where('name',$city['district'])->value('id');
			}
		}
		$data = array(
			'uid'		=> session('uid'),	//用户id
			'pid'		=> $pid,
			'cid'		=> $cid,
			'did'		=> $did,
			'address_detailed'=>$param['address_text'].' '.$param['address'],
			'address_tel'=>$param['mobile'],
			'address_name'=>$param['name'],
			'address_flag'=>'0',
			'address_lat'=>$param['lat'],
			'address_lng'=>$param['lng'],
			'remark'=>'',//需求上没有
			'isvalid'=>'1',
		);
		$id = Db::name('user_address_info')->insertGetId($data);
		if($id){
			return json_encode(['data' => [], 'msg' => 'success','code' => '200'], 320);
		}else{
			return json_encode(['data' => [], 'msg' => 'success','code' => '202'], 320);
		}
	}
	/*
	 *设为默认地址
	 */
    public function edit_state(){
    	$id    = $this->request->param('id',0,'intval');
    	$state = $this->request->param("state",0);
        if(!empty($id)){
        	$result = Db::name("user_address_info")->where("address_flag","=",1)->update(["address_flag" => 0]);
        }
        	$result = Db::name("user_address_info")->where("id",$id)->update(["address_flag" => 1]);
        if($result == false){
        	return jsonResponse('202','设置失败');
        }else{
        	return jsonResponse('200','success',$result);
        }
    }

	/**
	 *编辑用户收货地址
	 */
	public function edit_address(){
		$id = $this->request->param('id','','intval');
		if($id){
			$data = Db::name("user_address_info")->where("id",$id)->find();
			if(!empty($data)){
				return jsonResponse('200','success',$data);
			}
		}else{
				return jsonResponse('202','无法获取id');
		}
		
	}

	/**
	 *编辑用户收货地址提交
	 */
	public function edit_address_option(){
		$id = $this->request->param('id','','intval');
		if( $id ){
			$post = $this->request->param();
			unset($post['/api/address/edit_address_option']);
			$result = $this->validate($post,'Address.edit');
			$post['update_time'] = time();
			$result = Db::name("user_address_info")->where(['id' => $post['id']])->update($post);
			if($result =='1'){
				return jsonResponse('200','success',$result);
			}
		}else{
			return jsonResponse('202','无法获取id');
		}
	}
	/**
	 *删除收货地址
	 */
	public function del_address(){
		$id = $this->request->param('id','','intval');
	    if(!empty($id)){
	    	$result = Db::name("user_address_info")->where("id",$id)->update(["isvalid" => 0]);
	    }
		if($result == false){
			return jsonResponse('200','删除失败');
		}else{
			return jsonResponse('200','success',$result);
		}
	}

	protected function http_request($url, $data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }        
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
}