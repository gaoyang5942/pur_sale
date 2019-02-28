<?php
namespace app\api\controller;

use think\Exception;
use think\Db;
use app\api\model\CartInfo;
use app\api\model\PharmacyDrug;
use app\api\model\PharmacyInfo;
use app\api\model\UserAddress;
use app\api\model\Order;
use app\api\model\OrderStatus;
use app\api\model\PharmacyUser;
use app\api\controller\WeinsController;

class OrderController extends ApiBaseController
{
    public function _initialize()
    {
        parent::_initialize();

        $this->openid = session('wechat_user.id');
        $this->agent_appid = config('AGENT_APPID');
        $this->agent_secret = config('AGENT_SECRET');
        $this->appid = config('APPID');
        $this->secret = config('APPSECRET');
        $this->pay_key = config('YB_PAY_KEY');
        $this->base_url = config('PAYINSURANCE_URL');
        $this->sub_mchid = config('AGENT_SUB_MCHID');
        $this->mch_id = config('MCHID');
    }

    public static $need_auth = ['create', 'store', 'get_wait_shipping', 'detail_info'];

    // 购物车商品信息
    private $cart_products = [];

    // 药店信息
    private $pharmacy = [];

    // 费用信息
    private $totals = [
        'sub_total'   => 0.00, // 商品总金额
        'medical_pay' => 0.00, // 医保金额
        'self_pay'    => 0.00, // 自付金额
        'discount'    => 0.00, // 折扣金额
        'shipping'    => 0.00, // 运费金额
        'total'       => 0.00, // 实际支付金额
    ];

    // 订单地址
    private $shipping_address = [];

    /**
     * 获取创建订单信息
     * @return [type] [description]
     */
    public function create()
    {
        if (request()->isPost()) {
            $params     = request()->param();
            $order_type = $params['order_type'] ?? -1;
            $cart_ids   = !empty($params['cart_ids']) ? explode(',', $params['cart_ids']) : [];
            $address_id = $params['address_id'] ?? 0;

            try {
                $this->validateParams($order_type, $cart_ids);
                $ret['pharmacy']  = $this->pharmacy;
                $ret['cart_drug'] = $this->cart_products;
                $ret['total']     = $this->getTotals();

                // 获取收货地址
                $ret['shipping_address'] = [];
                if ($order_type == 1) {
                    $user_addr = new UserAddress;
                    $ret['shipping_address'] = $user_addr->getAddress(session('uid'), $address_id);
                }

                return json(['error' => 0, 'msg' => '', 'data' => $ret]);
            } catch (Exception $ex) {
                return json(['error' => 1, 'msg' => $ex->getMessage()]);
            }
        }

        return json(['error' => 1, 'msg' => '访问有误']);
    }

    /**
     * 保存订单
     * @return [type] [description]
     */
    public function store()
    {
        $data = [];
        if (request()->isPost()) {
            $data['post'] = request()->param();

            $order_type   = $data['post']['order_type'] ?? -1;
            $cart_ids   = !empty($data['post']['cart_ids']) ? explode(',', $data['post']['cart_ids']) : [];
            $address_id   = $data['post']['address_id'] ?? 0;

            try {
                // 基础参数校验
                $this->validateParams($order_type, $cart_ids);
                $data['pharmacy']  = $this->pharmacy;
                $data['cart_drug'] = [];
                $data['totals']     = $this->getTotals();
                if ($data['post']['order_type'] == 2) {
                    $dis = Db::name('distributor_store')->where(['pid'=>$this->pharmacy['id']])->select()->toArray();
                    if (empty($dis)) {
                        throw new Exception("当前药店没有配送员");
                    }
                }
                // 生成订单时的额外参数校验
                $this->validateCreateOrder($data['post']);
                $data['address']   = $this->shipping_address;
                // 格式化购物车药品信息
                foreach ($this->cart_products as $v) {
                    $drug_info = $v['drug'];
                    unset($v['drug'], $drug_info['default']);
                    $data['cart_drug'][] = array_merge($v, $drug_info);
                }

                $data['user_id'] = session('uid');

                $order_model = new Order();
                $order_id = $order_model->store($data);

                if (empty($order_id)) {
                    throw new Exception("订单生成失败，请稍后重试");
                }

                return json(['error' => 0, 'msg' => '', 'data' => ['order_id' => $order_id]]);
            } catch (Exception $ex) {
                return json(['error' => 1, 'msg' => $ex->getMessage()]);
            }
        }

        return json(['error' => 1, 'msg' => '访问有误']);
    }

    /**
     * 校验购物车商品
     * @param  [type] $order_type 支付类型 1.线上购药 2.线下购药
     * @param  [type] $cart_ids   订单id数组
     * @return [type]             [description]
     */
    private function validateParams($order_type, $cart_ids)
    {
        if (!in_array($order_type, [1, 2])) {
            throw new Exception('选择的支付方式有误');
        }

        if (empty($cart_ids)) {
            throw new Exception('请先选择要结算的产品');
        }

        $this->cart_products = CartInfo::with('drug.drugdetail')
            ->where('uid', $this->user_info['id'])
            ->whereIn('id', $cart_ids)
            ->field('did, cart_drug_num, pid, cart_drug_status')
            ->select()->toArray();
        if (empty($this->cart_products)) {
            throw new Exception('请先选择要结算的产品');
        }
        foreach ($this->cart_products as $v) {
            // 传递过来的要购买的商品不存在，或购物车中不存在该商品
            if (empty($v['drug'])) {
                throw new Exception('要结算的商品有误');
            }

            // 商品在店铺中已下架
            if ($v['drug']['isvalid'] == 0) {
                throw new Exception('药品' . $v['drug']['drug_store_name'] . '已下架，不能购买');
            }

            // 商品库存不足
            if ($v['drug']['drug_store_num'] < $v['cart_drug_num']) {
                throw new Exception('药品' . $v['drug']['drug_store_name'] . '库存不足，请重新选择');
            }
        }

        $store_ids = array_unique(array_column($this->cart_products, 'pid'));
        if (count($store_ids) != 1) {
            throw new Exception('只能选择同一店铺的商品进行支付');
        } else {
            $this->pharmacy = PharmacyInfo::get($store_ids[0])->toArray();
        }
        if (empty($this->pharmacy) || $this->pharmacy['isvalid'] == 0) {
            throw new Exception('店铺已关闭, 不能购买');
        }
        if (strtotime($this->pharmacy['end_store_time']) < strtotime(date('H:i',time())) || strtotime($this->pharmacy['start_store_time']) > strtotime(date('H:i',time()))) {
            throw new Exception('不在店铺营业期间之内，不能购买');
        }
        if ($this->pharmacy['pharmacy_isdoor'] == 0 && $order_type == 1) {
            throw new Exception('该店铺不支持送货上门');
        }
    }


    /**
     * 生成订单时的参数额外验证
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    private function validateCreateOrder($post)
    {
        // 验证店铺是否支持线上付款
        if ($this->pharmacy['pharmacy_isonline'] == 0 && $post['drugorder_payment_method'] == 1) {
            throw new Exception("该店铺不支持线上付款");
        }

        // 校验收货地址
        if ($post['order_type'] == 1) {
            // 检验用户选择的配送时间是否在有效期内
            $time = strtotime(date('H:i'));
            if($post['arrive_time'] == 'immediately' && ($time > strtotime($this->pharmacy['end_dispatch_time']) || $time < strtotime($this->pharmacy['start_dispatch_time'])))
            {
                throw new Exception('选择的配送时间有误');
            }
            if ($post['arrive_time'] != 'immediately' && (strtotime($post['arrive_time']) > strtotime($this->pharmacy['end_dispatch_time']) || strtotime($post['arrive_time']) < strtotime($this->pharmacy['start_dispatch_time']))) {
                throw new Exception('选择的配送时间有误');
            }

            // 校验收货地址是否有效
            $user_addr = new UserAddress;
            $shipping_address = $user_addr->getAddress(session('uid'), $post['address_id']);
            if (empty($shipping_address)) {
                throw new Exception('收获地址有误');
            }

            // TODO 收货地址距离的验证


            $this->shipping_address = [
                'id'       => $post['address_id'],
                'name'     => $shipping_address['address_name'],
                'tel'      => $shipping_address['address_tel'],
                'detailed' => $shipping_address['address_detailed'],
                'lat'      => $shipping_address['address_lat'],
                'lng'      => $shipping_address['address_lng'],
            ];
        } else {
            if (empty($post['address_name']) || empty($post['address_tel'])) {
                throw new Exception('上门取货的用户信息有误');
            }

            $this->shipping_address = [
                'name' => $post['address_name'],
                'tel'  => $post['address_tel'],
            ];
        }
    }

    /**
     * 获取相关费用
     * @return [type] [description]
     */
    private function getTotals()
    {
        // 计算自付金额和医保支付金额
        foreach ($this->cart_products as $v) {
            $this->totals['sub_total'] += $v['cart_drug_num'] * $v['drug']['drug_store_activity_price'];
            $this->totals['discount'] += $v['cart_drug_num'] * ($v['drug']['drug_store_retail_price'] - $v['drug']['drug_store_activity_price']);

            if ($this->pharmacy['pharmacy_ismed'] == 1 && !empty($v['drug']['drugdetail']) && $v['drug']['drugdetail']['is_medicalinurance'] == 1) {
                $this->totals['medical_pay'] += $v['cart_drug_num'] * $v['drug']['drug_store_activity_price'];
            } else {
                $this->totals['self_pay'] += $v['cart_drug_num'] * $v['drug']['drug_store_activity_price'];
            }
        }

        $this->totals['total'] = $this->totals['medical_pay'] + $this->totals['self_pay'];
        // TODO 如果有运费折扣信息，则修改下面代码，判断total > x, 则shipping = y;
        $this->totals['shipping'] = $this->pharmacy['deliver_fee'] + 0;//转为数字类型

        $this->totals['total'] += $this->totals['shipping'];

        return $this->totals;
    }

    /**
     * 根据状态获取订单
     * @return array 订单信息
     */
    public function get_orders_by_status()
    {
        $msg = '';
        $order_info = [];
        $code = 200;

        $status = $this->request->param('status');
        $uid = session('uid');
        $where = $uid ? "isvalid=1 AND uid=$uid" : '0=1';
        
        switch($status)
        {
            case 'wait_shipping':
                $where .= " AND (drugorder_payment_method=1 AND drugorder_status=".OrderStatus::PAYED." OR drugorder_payment_method=2 AND drugorder_status=".OrderStatus::CREATE.")";
                break;
            case 'wait_receive':
                $where .= " AND drugorder_status=".OrderStatus::SHIPPING;
                break;
            case 'wait_pay':
                $where .= " AND drugorder_status=".OrderStatus::WAIT_PAY;
                break;
            case 'all':
                break;
            case 'post_sale':
                $where .= ' AND drugorder_status IN('.OrderStatus::REFUNDING.','.OrderStatus::REFUNDED.')';
                break;
            default:
                $where = '0=1';
        }

        try
        {
            $order_info = Order::with('orderproducts.detail,pharmacy')
                ->where($where)
                ->select();
        }
        catch(Exception $e)
        {
            $code = $e->getCode();
            $msg = $e->getMessage();
        }

        return ['code' => $code, 'order_info' => $order_info, 'msg' => $msg];
    }

    public function detail_info()
    {
        $msg = '';
        $order_detail = [];
        $code = 200;

        $drugorder_number = $this->request->param('order_no');
        if(!$drugorder_number)
        {
            return ['code' => 601, 'order_detail' => $order_detail, 'msg' => '参数错误'];
        }

        try
        {
            $order_detail = Order::with('pharmacy,orderproducts.detail,address,scoreinfo')
                ->where(['id' => $drugorder_number, 'isvalid' => 1])
                ->find();
        }
        catch(\Exception $e)
        {
            $code = 602;
            $msg = $e->getMessage();
        }

        return ['code' => $code, 'order_detail' => $order_detail, 'msg' => $msg];
    }

    /**
     * 扫码获取订单信息
     * @return [type] [description]
     */
    public function get_order_detail()
    {
        $msg = '';
        $order_detail = [];
        $code = 200;

        $drugorder_number = $this->request->param('order_no');
        if(!$drugorder_number)
        {
            return ['code' => 601, 'order_detail' => $order_detail, 'msg' => '参数错误'];
        }

        try
        {
            $order_detail = Order::with('pharmacy,orderproducts.detail,scoreinfo')
                ->where(['id' => $drugorder_number, 'isvalid' => 1])
                ->find();
        }
        catch(\Exception $e)
        {
            $code = 602;
            $msg = $e->getMessage();
        }
        
        return ['code' => $code, 'order_detail' => $order_detail, 'msg' => $msg];
    }
    /**
     * 获取订单状态
     * @return array 获取订单状态
     */
    public function get_status()
    {
        try {
            $drugorder_number = $this->request->param('order_no'); 
            $order_status = Order::where('drugorder_number', $drugorder_number)->value('drugorder_status');
            return ['code' => 200, 'status' =>$order_status];
        } catch (Exception $e) {
             return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }
    }
    /**
     * 取消订单
     * @return array 取消订单结果
     */
    public function cancel_order()
    {
        try
        {
            $drugorder_number = $this->request->param('order_no');
            Order::where('drugorder_number', $drugorder_number)->update(['drugorder_status' => 9]);
            return ['code' => 200, 'msg' => '订单取消成功'];
        }
        catch(Exception $e)
        {
            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除订单
     * @return array 删除订单结果
     */
    public function delete_order()
    {
        try
        {
            $drugorder_number = $this->request->param('order_no');
            Order::where('drugorder_number', $drugorder_number)->update(['isvalid' => 0]);

            return ['code' => 200, 'msg' => '订单删除成功'];
        }
        catch(Exception $e)
        {
            return ['code' => $e-totals>getCode(), 'msg' => $e->getMessage()];
        }
    }

    /**
     * 退款（待完善）
     * @return [type] [description]
     */
    public function refund_order()
    {
        //调用WeinsController控制器中refund接口退款
    }
    //更新订单uid
    public function update_order_uid(){
        $drugorder_number = $this->request->param('order_no');
        $drugorder_type = $this->request->param('jump_t');
        $uid = session('uid');
        $is_uid = Db::name('pharmacy_drug_order')->where('id',$drugorder_number)->value('uid');
        if($drugorder_type == 'q'){
            return ['code' => 200, 'msg' => ''];
        }else{
            if( $uid == $is_uid ){
                return ['code' => 200, 'msg' => ''];
            }else{
                if($uid && $is_uid == 0){
                    $wd = Db::name('pharmacy_drug_order')->where('id',$drugorder_number)->update(['uid' =>$uid ]);
                    if($wd){
                        return ['code' => 200, 'msg' => ''];
                    }else{
                        return ['code' => 202, 'msg' => '订单领取失败！'];
                    }
                }else{
                    return ['code' => 202, 'msg' => '订单已被领取！'];
                } 
            }
        }
    }
    //扫码后领取订单
    public function get_order(){
        $drugorder_number = $this->request->param('order_no');
        $uid = session('uid');
        $ubput = Db::name('pharmacy_drug_order')->where('id', $drugorder_number)->update(['uid' => $uid, 'drugorder_status' => 5]);
        if($ubput){
            return ['code' => 200, 'msg' =>$drugorder_number];
        }else{
            return ['code' => 202, 'msg' =>'领取失败'];
        }
       
    }
}
