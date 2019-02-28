<?php 
namespace app\api\controller;
use base\controller\HomeBaseController;
use think\Exception;
use think\Db;
Class ApitemplateController extends HomeBaseController{
	//
	public function _initialize(){
		$this->agent_appid = config('AGENT_APPID');
		$this->agent_secret = config('AGENT_SECRET');
		$this->appid = config('APPID');
		$this->base_url = config('PAYINSURANCE_URL');
	}
	//接收扫码枪
	public function getAuthcode(){
		$param = $this->request->param();
		$authCode= $param['code'];
		$orderid = $param['orderid'];
		$pharamcy_id = $param['pid'];
		$pname = $param['pname'];
		$money = $param['money'];
		$time = $param['time'];
		$mchid = $param['mchid'];
		$or_id = $param['order_id'];
		$url = 'https://card.wecity.qq.com/mchapi';
    	$data = array(
	    			"bizid"=>$this->agent_appid,
	    			"cmdid"=>"idqrquery",
	    			"nonce"=>set_nonce(32,0),
	    			"req"=>array("qrcode"=>$authCode)
    				);
    	$res = https_post($url,json_encode(data_to_signdata($data,config('WXBK_KEY')),JSON_UNESCAPED_UNICODE));
    	$json = json_decode($res);
    	// $json = true;
    	// $wx_check_sign = true;
		if(is_object($json) && $json->code == 0){
    	//if($json){
    		//if($wx_check_sign){
			if(wx_check_sign($json,config('WXBK_KEY'))){
				$openid = $json->rsp->user_id;
    			//$openid = 'owIuSxK0r3Isen7aZMND3l4wxdks';
				//写接口查询是否绑卡chick_userbind
				$bindurl = "https://wx.bcyb.gov.cn/api/user/chick_userbind";     
            	$bindres = https_post($bindurl,['openid',$openid]); 
            	$result = json_decode($bindres, true);
				if($result['code'] != 202){
					//跳转绑卡
					echo json_encode(['data'=>'未绑卡','code'=>'600','state'=>'0']);;
				}else{
					 //更新订单表里的信息,openid 
		            $user = Db::name('pharmacy_user_info')->where('userinfo_openid',$openid)->value('id');
		            if(!$user){
		                $url = "https://wx.bcyb.gov.cn/api/user/get_sys_info?openid=".$user;     
		                $res = https_post($url);  
		                $result = json_decode($res, true);
		                $data = array(
		                    'userinfo_openid'   =>  $param['Openid'],
		                    'userinfo_username' =>  $result['data']['name'],
		                    'userinfo_sex'      =>  $result['data']['gender'],
		                    'userinfo_card_id'  =>  $result['data']['birthday'],
		                    'userinfo_phone'    =>  decode($result['data']['tel'],'ENCODE_STRING'),
		                    'create_time'       =>  time(),
		                    'userinfo_type'     =>  1,
		                    'userinfo_state'    =>  1,
		                    'isvalid'           =>  1
		                );
		                $user = Db::name('pharmacy_user_info')->insertGetId($data);
		            }
		            if($orderid && $user){
		                $tu = Db::name('pharmacy_drug_order')->where('drugorder_number',$orderid)->update(['uid'=>$user,'drugorder_status'=>5]);
		            }
		            if($tu){
		            	//发送模版消息
	                    $jump_url = 'http://'.$_SERVER['SERVER_NAME'].'/wechat/order/get_order?order_no='.$or_id.'&jump_type=q';
						$data_tem = [
		                    'touser'=>$openid,
		                    'template_id'=>'JFieiM0mOLX_qfqZNu3Et-q04gqEBdY4u4i4BcMSlt8',
		                    'url'=>$jump_url,//跳转订单详情
		                    'data'=>[
	                            'first'   =>['value'=>'你有一笔新的支付费用，请核对并支付。订单详情如下','color'=>'#173177'],
	                            'keyword1'=>['value'=>$orderid,'color'=>'#173177'],
	                            'keyword2'=>['value'=>$pname,'color'=>'#173177'],
	                            'keyword3'=>['value'=>round($money/100,2).'元','color'=>'#173177'],
	                            'keyword4'=>['value'=>$time,'color'=>'#173177'],
	                            'remark'=>['value'=>'感谢您的支持','color'=>'#173177']
	                            ],
				        ];
						$access_token = $this->get_access_token();
						if(!$access_token){
							echo json_encode(['data'=>'access_token获取失败！','code'=>'202','state'=>'0']);
						}
						$urlss = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;

						$data_tem = json_encode($data_tem);

						$res = https_post($urlss, $data_tem);
						$res = json_decode($res,true);
						if(!empty($res) && $res['errmsg'] == 'ok' && $res['errcode'] == '0'){
						    $datas = ['data'=>'推送发送成功！','code'=>'200','state'=>'0'];
						    Db::name('pharmacy_drug_order')->where('drugorder_number',$orderid)->update(['qian_code'=>$authCode]);
						}else{
							$datas = ['data'=>'推送发送失败！','code'=>'202','state'=>'0'];
						}
						echo json_encode($datas);
		            }else{
		            	echo json_encode(['data'=>'订单领取失败','code'=>'500','state'=>'0']);
		            }
					
				}
			}
		}else{
			echo json_encode(['data'=>'code过期，请重新扫码','code'=>'500','state'=>'0']);
		}
	}
	/**
	 * 获取access_token接口
	 * @return array access_token信息
	 */
	private function get_access_token()
	{
		$url = "https://api.weixin.qq.com/cgi-bin/token";
		$data = array(
			'grant_type' 	=> 'client_credential',
			'appid'			=> config('appid'),
			'secret'		=> config('appsecret')
		);
		$res = https_post($url,$data);
		$result = json_decode($res, true);
		$access_token = $result["access_token"];
		return $access_token;
	}
}