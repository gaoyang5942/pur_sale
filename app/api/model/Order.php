<?php
namespace app\api\model;

use think\Model;
use think\Db;
use EasyWeChat\Factory;
use think\Request;

class Order extends Model
{
    protected $table = 'yb_pharmacy_drug_order';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    public function products()
    {
        return $this->hasMany('app\api\model\OrderProducts', 'drugorder_number', 'drugstore_number');
    }


    public function store($data)
    {
        $order_id = 0;

        Db::transaction(function() use (&$order_id, $data) {
            extract((array) $data);
            $current_time = time();
            $order_no = $this->getOrderNo();
            if ($post['drugorder_payment_method'] == 1) {
                $status = OrderStatus::WAIT_PAY;
            }else{
                $status = OrderStatus::CREATE;
            }
            // 保存订单
            $order_id = Db::table('yb_pharmacy_drug_order')->insertGetId([
                'drugorder_number'            => $order_no,
                'pid'                         => $pharmacy['id'],
                'uid'                         => $user_id,
                'drugorder_self_fee'          => $totals['self_pay'],
                'drugorder_medical_fee'       => $totals['medical_pay'],
                'drugorder_transport_fee'     => $totals['shipping'],
                'drugorder_total_fee'         => $totals['sub_total'],
                'drugorder_pre_fee'           => $totals['discount'],
                'drugorder_amount_payment'    => $totals['total'],
                'drugorder_payment_method'    => $post['drugorder_payment_method'],
                'drugorder_status'            => $status,
                'drugorder_settlement_state'  => 0,
                'drugorder_pay_id'            => 0,
                'address_name'                => $address['name'],
                'address_tel'                 => $address['tel'],
                'address_id'                  => $address['id'] ?? '',
                'address_lat'                 => $address['lat'] ?? '',
                'address_lng'                 => $address['lng'] ?? '',
                'isvalid'                     => 1,
                'drugorder_partnerid'         => 0,
                'drugorder_pick'              => $post['order_type'],
                'drugorder_preferential'      => 0,
                'remark'                      => $post['remark'],
                'isread'                      => 0,
                'drugorder_rid'               => 0,
                'drugorder_distribution_time' => $post['arrive_time'] ?? '',
                'drugorder_update_time'       => $current_time,
                'drugorder_create_time'       => $current_time,
                'drugorder_pay_type'          => 0,
                'drugorder_order_type'        => 1,
                'drugorder_ewm_img'           => '',
            ]);
            
            foreach ($cart_drug as $v) {
                // 保存订单商品
                Db::table('yb_drug_store_detailed')->insert([
                    'drugstore_number'    => $order_no,
                    'drugstore_code'      => $v['did'],
                    'drugstore_ori_price' => $v['drug_store_retail_price'],
                    'drugstore_pre_price' => $v['drug_store_activity_price'],
                    'drugstore_num'       => (int) $v['cart_drug_num'],
                    'drugstore_price'     => round($v['cart_drug_num'] * $v['drug_store_activity_price'], 2),
                ]);
                if ($post['drugorder_payment_method'] == 2) {
                    // 商品减库存
                    Db::table('yb_pharmacy_drug_store')->where('did', (int) $v['did'])
                        ->setDec('drug_store_num', (int) $v['cart_drug_num']);
                }
            }

            if($post['order_type'] == 1)//线上支付
            {
                $order_status = OrderStatus::WAIT_PAY;
            }
            else if($post['order_type'] == 2)//到店付款
            {
                $order_status = OrderStatus::WAIT_SHIPPING;
            }
            
            // 订单状态表更新
            DB::table('yb_order_status_history_info')->insert([
                'order_id'        => $order_id,
                'order_stauts_id' => $order_status,
                'create_time'     => $current_time,
            ]);
            //货到付款,直接分配配送员
            if ($post['order_type'] == 1 && $post['drugorder_payment_method'] = 2)
            {
                $drugorder_rid = $this->getrid($pharmacy['id']);
                $this->setMage($drugorder_rid,$order_no);
                self::where(['drugorder_number'=>$order_no])->update(['drugorder_rid'=>$drugorder_rid]);
            }
            // 删除购物车
            Db::name('cart_info')->whereIn('id', $post['cart_ids'])->update(['cart_drug_status' => 0]);
        });

        return $order_id;
    }

    /**
     * 获取配送员id
     * @return [type] [description]
     */
    public function getrid($pid){
        $store = Db::name('distributor_store')->alias('s')
                    ->where(['pid'=>$pid,'isvalid'=>0])
                    ->select()
                    ->toArray();
        foreach ($store as $key => $value) {
            $count[$key]['count'] = self::where(['drugorder_rid'=>$value['distributor_id']])->count();
            $count[$key]['drugorder_rid'] = $value['distributor_id'];
        }
        if (count($store) != 1) {
            array_multisort(array_column($count,'count'),SORT_ASC,$count);
        }
        return $count[0]['drugorder_rid'];
    }

    /**
     * 给配送员推送配送消息
     * @param  $disid    string  配送员id
     * @param  $ordernum string  订单号
     * @return [type] [description]
     */
    public function setMage($disid,$ordernum){
        $order = Db::name('pharmacy_drug_order')->alias('o')
                    ->field('d.drug_detailed_name,s.drugstore_num,o.drugorder_transport_fee,i.province,i.city,i.district,i.pharmacy_address,o.address_id,o.address_lat,o.address_lng')
                    ->join(['yb_drug_store_detailed' => 's'],'o.drugorder_number = s.drugstore_number','LEFT')
                    ->join(['yb_pharmacy_drug_detailed' => 'd'],'s.drugstore_code = d.id','LEFT')
                    ->join(['yb_pharmacy_info' => 'i'],'o.pid = i.id','LEFT')
                    ->where(['o.drugorder_number' => $ordernum,'o.isvalid'=>array('neq',0)])
                    ->select()
                    ->toArray();
        //药品详细
        foreach ($order as $key => $value) {
            $drug .= $value['drug_detailed_name'].' ×'.$value['drugstore_num'].' ';
        }
        //取货地址
        $pprovince = Db::name('city_province')->where(['id' => $order[0]['province']])->value('name');
        $pcity     = Db::name('city_city')->where(['id' => $order[0]['city']])->value('name');
        $pdistrict = Db::name('city_district')->where(['id' => $order[0]['district']])->value('name');
        $pick      = $pprovince . $pcity . $pdistrict . $order[0]['pharmacy_address'];
        //配送地址
        $address   = Db::name('user_address_info')->where(['id'=>$order[0]['address_id']])->find();
        $dprovince = Db::name('city_province')->where(['id' => $address['pid']])->value('name');
        $dcity     = Db::name('city_city')->where(['id' => $address['cid']])->value('name');
        $ddistrict = Db::name('city_district')->where(['id' => $address['did']])->value('name');
        $delivery  = $dprovince . $dcity . $ddistrict . $address['address_detailed'];
        //配送元openid
        $openid    = Db::name('distributor_info')->where(['id'=>$disid])->value('distributor_openid');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $jump_url  = $protocol.$_SERVER['HTTP_HOST'].'/wechat/index/order?drugorder_number='.$ordernum.'&address_lat='.$order[0]['address_lat'].'&address_lng='.$order[0]['address_lng'];
        //消息模板内容
        $data_tem = [
            'touser'=>$openid,
            'template_id'=>'L1OGKGHuUZNZJZaKNFHb6SoIQ-d-6jGZ1ItmO75yGe8',
            'url'=>$jump_url,//跳转订单详情
            'data'=>[
                'first'    => ['value'=>'你有一个新的配送单,请尽快配送','color'=>'#173177'],
                'keyword1' => ['value'=>$drug,'color'=>'#173177'],
                'keyword2' => ['value'=>$pick,'color'=>'#173177'],
                'keyword3' => ['value'=>$delivery,'color'=>'#173177'],
                'keyword4' => ['value'=>round($order[0]['drugorder_transport_fee']/100,2).'元','color'=>'#173177'],
                ],
        ];
        //获取token
        $access_token = $this->get_access_token();
        $urlss = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $data_tem = json_encode($data_tem);
        $res = https_post($urlss, $data_tem);
    }

    /**
     * 获取订单编码
     * @return [type] [description]
     */
    public function getOrderNo()
    {
        // 如果已存在该order_no, 则重新生成
        do {
            $order_no = date('Ymd') . substr(time(), -3) . substr(microtime(), 2, 5);

            $count = self::where('drugorder_number', $order_no)->count();
        } while ($count > 0);

        return $order_no;
    }

    public function orderproducts()
    {
        return $this->hasMany('OrderProducts', 'drugstore_number', 'drugorder_number');
    }

    public function pharmacy()
    {
        return $this->hasOne('PharmacyInfo', 'id', 'pid');
    }
    
    public function address()
    {
        return $this->hasOne('UserAddress', 'id', 'address_id');
    }

    public function scoreinfo()
    {
        return $this->hasOne('UserScoreInfo', 'source_id');
    }

    // public function getDrugorderStatusAttr($value)
    // {
    //     $status = ['0' => '下单成功', '1' => '完成备药', '2' => '完成取药', '3' => '配送中', '4' => '送达', '5' => '等待付款', '6' => '付款完成', '7' => '退款中', '8' => '退款完成', '9' => '订单取消'];

    //     return $status[$value];
    // }

    public function getDrugorderCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    private function get_access_token()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.config('APPID').'&secret='.config('APPSECRET');
        $result = cmf_curl_get($url);
        $result_arr=json_decode($result,true);//将json转换成数组
        return $result_arr['access_token'];
    }
}
