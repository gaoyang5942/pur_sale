<?php
namespace app\wechat\controller;
use think\Exception;
use think\Db;
use think\Request;
use base\controller\HomeBaseController;
use EasyWeChat\Factory;

class CourierController extends HomeBaseController{

    /**
     * 注册骑手
     */
    public function save(){
    	$param = $this->request->param();
    	$id = $param['id'];
    	unset($param['id']);
    	unset($param['/api/courier/save_html']);
    	$courierId = Db::name('distributor_info')
    					->where(['distributor_phone' => $param['distributor_phone']])
    					->value('id');
    	if (!$courierId) {
    		$param['distributor_card_photo'] = $this->base64_image_content($param['distributor_card_photo']);
	    	$param['distributor_card_img']   = $this->base64_image_content($param['distributor_card_img']);
	    	$param['distributor_card_pic']   = $this->base64_image_content($param['distributor_card_pic']);
	    	$courierId = Db::name('distributor_info')->insertGetId($param);
    	}
    	$dis = Db::name('distributor_store')->where(['distributor_id' => $courierId,"pid" => $id])->find();
    	if (!$dis) {
    		$array['distributor_id']    = $courierId;
		    $array['pid']               = $id;
		    $array['distributor_time']  = time();
		    $array['distributor_state'] = '0';
		    $array['isvalid']           = '0';
		    Db::name('distributor_store')->insert($array);
		    $mess = "注册成功,请等待审批";
		    $code = 200;
    	}else{
    		$mess = "您已经是本药店的骑手";
    		$code = 400;
    	}
    	jsonResponse($code,$mess);
    }


    public function verify(){
    	$param = $this->request->param();
    	$info  = Db::name('distributor_info')->where(['distributor_openid' => $param['openid']])->find();
    	if ($info) {
    		$array['distributor_id']    = $info['id'];
		    $array['pid']               = $param['id'];
		    $array['distributor_time']  = time();
		    $array['distributor_state'] = '1';
		    $array['isvalid']           = '0';
		    Db::name('distributor_store')->insert($array);
		    $mess = "注册成功,请等待审批";
		    $code = 200;
    	}
    	jsonResponse($code,$mess);
    }


	/**
	 * [将Base64图片转换为本地图片并保存]
	 * @param  [Base64] $base64_image_content [要保存的Base64]
	 * @param  [目录] $path [要保存的路径]
	 */
	function base64_image_content($base64_image_content){
	    //匹配出图片的格式
	    $new_file = '';
	    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
	        $type = $result[2];
	        $new_file = "download/courierimg/".date('Ymd',time())."/";
	        if(!file_exists($new_file)){
	            //检查是否有该文件夹，如果没有就创建，并给予最高权限
	            mkdir($new_file, 0700);
	        }
	        $new_file = $new_file.time().rand(00000,99999).".{$type}";
	        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
	            return '/'.$new_file;
	        }else{
	            return false;
	        }
	    }else{
	        return false;
	    }
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
                'scopes'    => ['snsapi_base'],
            ],
        ];
                // 'callback'  => Request::instance()->domain().'/api/index/courier?id=2',
        return $config;
    }
    /**
     * 网页授权
     */
    public function oauth(){
    	$param  = $this->request->param();
        $config = $this->config();
        $config['oauth']['callback'] = Request::instance()->domain().'/wechat/courier/index?id='.$param['id'];
        $app    = Factory::officialAccount($config);
        $oauth  = $app->oauth;
		$oauth->redirect()->send();     
    }

    /**
     * 授权回调
     */
    public function index(){
    	$config = $this->config();
    	$app    = Factory::officialAccount($config);
        $oauth  = $app->oauth;
        $user   = $oauth->user();
        $user_arr = $user->original;
    	$param  = $this->request->param();
    	$this->assign('id',$param['id']);
    	$this->assign('openid',$user_arr['openid']);
    	return $this->fetch();
    }

    /**
     * 注册完成
     */
    public function ok(){
        return $this->fetch();
    }

    /**
     * 订单完成
     */
    public function oks(){
        return $this->fetch();
    }

}
