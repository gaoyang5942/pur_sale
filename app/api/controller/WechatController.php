<?php
namespace app\api\controller;

use base\controller\HomeBaseController;
use think\Db;
use EasyWeChat\Factory;
use think\Request;

Class WechatController extends HomeBaseController{
	//如果允许定位当前地址，做操作距离最近
	//获取药店详细信息
	public function pharmacy_datail(){
		$lat = session('latitude');
		$lon = session('longitude');
		$pharmacy_list = Db::name('pharmacy_info')->where('isvalid',1)->select()->toArray();
		if($lat && $lon){
			if(!empty($pharmacy_list)){
				foreach ($pharmacy_list as $key => $value) {
					$distance_km = $this->getDistance($lat,$lon,$value['pharmacy_lat'],$value['pharmacy_lng'],2,1);
					//此处可以根据距离判断多远可以不显示
					$clom_data[$key] = $value;
					$clom_data[$key]['pharmacy_store_pic'] = 'http://'.$_SERVER['SERVER_NAME'].'/upload/'.$value['pharmacy_store_pic'];
					$distance = $distance_km*1000;
					$clom_data[$key]['distance'] = $distance;
					$clom_data[$key]['distance_km'] = $distance_km;
					$discount = Db::name('pharmacy_activity')->where('sid',$value['id'])->value('val');
					if($discount > 0){
						$discount_c = $discount;
					}else{
						$discount_c = 0;
					}
					$clom_data[$key]['discount'] = $discount_c;
					if (strtotime($value['end_store_time']) < strtotime(date('H:i',time())) || strtotime($value['start_store_time']) > strtotime(date('H:i',time()))) {
						$clom_data[$key]['flag'] = 'N';
					}else{
						$clom_data[$key]['flag'] = 'Y';
					}
				}
				//按距离远近排序
				array_multisort(array_column($clom_data,'distance'),SORT_ASC,$clom_data);
			}
		}else{
			if(!empty($pharmacy_list)){
				foreach ($pharmacy_list as $key => $value) {
					$order_count = Db::name('pharmacy_drug_order')->where('pid',$value['id'])->count();
					$clom_data[$key] = $value;
					$clom_data[$key]['order_count'] = $order_count;
					$discount = Db::name('pharmacy_activity')->where('sid',$value['id'])->value('val');
					if($discount > 0){
						$discount_c = $discount;
					}else{
						$discount_c = 0;
					}
					$clom_data[$key]['discount'] = $discount_c;
					if (strtotime($value['end_store_time']) < strtotime(date('H:i',time())) || strtotime($value['start_store_time']) > strtotime(date('H:i',time()))) {
						$clom_data[$key]['flag'] = 'N';
					}else{
						$clom_data[$key]['flag'] = 'Y';
					}
				}
				//按距离远近排序
				array_multisort(array_column($clom_data,'order_count'),SORT_DESC,$clom_data);
			}
		}
		//判断是否在营业时间范围内
		return json_encode(['data' => $clom_data, 'msg' => 'success','code' => '200'],320);
	}
	//获取坐标信息
	public function get_location(){
		$param = $this->request->param();
		$location = $param['lat'].','.$param['lon'];
		session('latitude',$param['lat']);
		session('longitude',$param['lon']);
		$url = "https://apis.map.qq.com/ws/geocoder/v1/?location=".$location."&get_poi=1&key=".config('qq_map_key');
		$res = $this->http_request($url);
		$result = json_decode($res, true);
		$loc_arr = $result['result']['pois'];
		$loc_arr[0]['status'] = $result['status'];
		$loc = $loc_arr[0];
		return json_encode(['data' => $loc, 'nearly'=>$result['result']['pois'], 'msg' => 'success','code' => '200'],320);
	}
	//获取用户信息
	public function get_user_info(){
		$openid = session('wechat_user.id');
        if (empty($openid)){ //微信openid 判断是否授权
            return json_encode(['data' => [], 'msg' => 'success','code' => '202'], 320);
        }else{
            $data['openid']       = session('wechat_user.id');
            //隐藏姓后面的字符
            $data['nickname']     = $this->substr_cut(session('wechat_user.nickname'));
            $data['headimgurl']   = session('wechat_user.avatar');
            $data['state_type']   = session('wechat_user.state_type');
            //判断用户是否授权成功
            $user = Db::name('pharmacy_user_info')->where("userinfo_openid",$openid)->find();
            if(empty($user)){
                //授权失败返回授权信息
                return json_encode(['data' => [], 'msg' => '授权失败','code' => '202'], 320);
            }else{
            	return json_encode(['data' => $data, 'msg' => 'success','code' => '200'],320);
            }
        }
	}
	/**
	 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
	 * @param string $user_name 姓名
	 * @return string 格式化后的姓名
	 */
	public function substr_cut($user_name){
	    $strlen     = mb_strlen($user_name, 'utf-8');
	    $firstStr   = mb_substr($user_name, 0, 1, 'utf-8');
	    $lastStr    = mb_substr($user_name, -1, 1, 'utf-8');
	    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 1) . $lastStr;
	}
	//查看地址
	public function my_address(){
		$uid = session('uid');
		$address_list = Db::name('user_address_info')->where('uid',$uid)->select()->toArray();
		if(empty($address_list)){
			return json_encode(['data' => [], 'msg' => '添加自己的地址哦','code' => '202'], 320);
		}else{
			foreach ($address_list as $key => $value) {
				if($value['isvalid'] == 1){
					$clom[$key]['id'] 					= $value['id'];
					$clom[$key]['city_province'] 		= Db::name('city_province')->where('id',$value['pid'])->value('name');
					$clom[$key]['city_city'] 			= Db::name('city_city')->where('id',$value['cid'])->value('name');
					$clom[$key]['city_district']		= Db::name('city_district')->where('id',$value['did'])->value('name');
					$clom[$key]['address_detailed'] 	= $value['address_detailed'];
					$clom[$key]['address_tel'] 			= $value['address_tel'];
					$clom[$key]['address_name'] 		= $value['address_name'];
					$clom[$key]['address_flag'] 		= $value['address_flag'];
					$clom[$key]['address_lat'] 			= $value['address_lat'];
					$clom[$key]['address_lng'] 			= $value['address_lng'];
				}
			}
			if(!empty($clom)){
				$clom_data = array_values($clom);
			}else{
				$clom_data = [];
			}
			return json_encode(['data' => $clom_data, 'msg' => 'success','code' => '200'], 320);
		}
	}
	//新增地址操作
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
			return json_encode(['data' => [], 'msg' => '添加失败','code' => '202'], 320);
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
	//获取session
	public function get_session(){
		$data = array(
			'latitude' 	=> session('latitude'),
			'longitude' => session('longitude'),
		);
		return json_encode(['data' => $data, 'msg' => 'success','code' => '200'], 320);
	}
	/**
	* 微信签名及配置
	* @return  [type] $signPackage        [
	*                                     		appId 			=> '公众号的唯一标识',
	*                                 			nonceStr        => '生成签名的随机串',
	*  								  			timestamp       => '生成签名的时间戳',
	*                                 			url             => '当前地址',
	*                                 			signature       => '签名',
	*                             ]
	*/
	//获得签名包
	public function getSignPackage() {
		$param = $this->request->param();
		if( $param['lat'] && $param['lon'] ){
			$clom_r = '?lat='.$param['lat'].'&lng='.$param['lon'];
		}
		$jsapiTicket = $this->getJsApiTicket();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]".'/wechat/index/index'.$clom_r;
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId"     => config('APPID'),
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
		);
		return $signPackage;
	}
	//获得签名包
	public function we_getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]".'/wechat/index/wescan';
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId"     => config('APPID'),
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
		);
		return $signPackage;
	}
	//获得JS API的ticket
	public function getJsApiTicket(){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".config('APPID')."&secret=".config('APPSECRET');
		$res = $this->http_request($url);
		$result = json_decode($res, true);
		$access_token = $result["access_token"];
		if ($access_token){
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$access_token;
			$res = $this->http_request($url);
			$result = json_decode($res, true);
			$jsapi_ticket = $result["ticket"];
		}
		return $jsapi_ticket;
	}
	//生成长度16的随机字符串
	public function createNonceStr($length = 16){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	//HTTP请求（支持HTTP/HTTPS，支持GET/POST）
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
	//配置
	public function config(){
        $config = [
            'app_id'        => config('APPID'),
            'secret'        => config('APPSECRET'),
            'token'         => '92rj12wRIjCeL3dwuWvqWHVCIGlPrC7t',
            'aes_key'       => config('AESKey'),
            'response_type' => 'array',
            'oauth'         => [
                'scopes'    => ['snsapi_userinfo'],
                'callback'  => Request::instance()->domain().'/api/wechat/oauth_callback',
            ],
        ];
        return $config;
    }
    /**
     * 网页授权
     */
    public function oauth(){
    	session('referer',$_SERVER['HTTP_REFERER']);
        $config = $this->config();
        $app    = Factory::officialAccount($config);
        $oauth  = $app->oauth;
        $oauth->redirect()->send();
    }

    /**
     * 授权用户回调
     */
    public function oauth_callback(){
        $config = $this->config();
        $app    = Factory::officialAccount($config);
        $oauth  = $app->oauth;
        $user   = $oauth->user();
        $user_arr = $user->original;
    	$uid = Db::name('pharmacy_user_info')->where("userinfo_openid",$user->id)->value('id');
		if($openid){
			$url = "https://wx.bcyb.gov.cn/api/user/get_sys_info?openid=".$user->id;     
			$res = $this->http_request($url);  
			$result = json_decode($res, true);
		}
        if(!$uid &&  $user->id){
            $uid = Db::name('pharmacy_user_info')->insert([
                'userinfo_openid'       =>   $user->id,
                'userinfo_username'		=>	 $result['data']['name'],
				'userinfo_sex'			=> 	 $user_arr['sex'],
				'userinfo_card_id'		=>	 $result['data']['card_id'],
				'userinfo_phone'		=>	 decode($result['data']['tel'],'ENCODE_STRING'),
				'userinfo_birthday'		=>	 $result['data']['birthday'],	
				'userinfo_email'		=>	'',
				'userinfo_membership_grade'		=>	'',
				'userinfo_historical_int'		=>	'',
				'userinfo_consumption_num'		=>	'',
				'userinfo_convertibility_int'	=>	'',
				'userinfo_available_int'		=>	'',
				'create_time'			=>	time(),
				'userinfo_type'			=>	'1',
				'userinfo_state'		=>	'1',
				'isvalid'				=>	'1',
            ]);
        }else{
            Db::name('pharmacy_user_info')->where("userinfo_openid",$user->id)->update([
                'userinfo_username'     =>   $result['data']['name'],
                'userinfo_sex'			=> 	 $user_arr['sex'],
                'userinfo_card_id'		=>	 $result['data']['card_id'],
				'userinfo_phone'		=>	 decode($result['data']['tel'],'ENCODE_STRING'),
				'userinfo_birthday'		=>	 $result['data']['birthday'],
            ]);
        }
        $user_arr = $user->toArray();
        if($result['data']['state_type']){
        	$user_arr['state_type'] = $result['data']['state_type']; 
        }
        session('wechat_user', $user_arr);
        session('uid',$uid);
        $referer = session('referer');
        $this->redirect($referer);
    }

     /**
     * 静默授权
     */
    public function base_oauth(){
        $area_code = $this->request->param('area_code', '');//统筹区编码
        $xz_name = $this->request->param('xz_name', ''); //险种统筹区
        $xz = $this->request->param('xz', '');
        $shuffle = '';

        if(!empty($area_code) || !empty($xz_name) || !empty($xz)){
            //$result = Db::name("wx_pay_config")->where('area_code',$area_code)->where('xz_name', $xz_name)->find();
            $shuffle = '?area_flag=' . $area_code . '_' . $xz;
        }

        $config = [
            'app_id'        => config('appid'),
            'secret'        => config('appsecrect'),
            'token'         => '92rj12wRIjCeL3dwuWvqWHVCIGlPrC7t',
            'aes_key'       => config('aeskey'),
            'response_type' => 'array',
            'oauth'         => [
                'scopes'    => ['snsapi_userinfo'],
                'callback'  => Request::instance()->domain().'/api/wechat/base_oauth_callback' . $shuffle,
            ],
        ];
        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;
        $oauth->redirect()->send();
    }

    /**
     * 静默授权回调
     */
    public function base_oauth_callback()
    {
        $area_flag = $this->request->param('area_flag', '');
        $shuffle = $area_flag ? '_' . $area_flag : '';

        $config = $this->config();
        $config['callback'] =  Request::instance()->domain().'/api/wechat/base_oauth_callback';
        $app      = Factory::officialAccount($config);
        $oauth    = $app->oauth;
        $user     = $oauth->user();
        $openid   = $user->id;
        session('wechat_user' . $shuffle,$user->toArray());
        $this->redirect($shuffle ? session('callback_url') : url(session('callback_url')));
    }
}

?>
