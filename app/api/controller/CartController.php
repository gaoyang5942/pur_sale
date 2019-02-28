<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 9:33
 */

namespace app\api\controller;


use base\controller\HomeBaseController;
use think\Db;
use think\Request;

class CartController extends ApiBaseController
{
    const DRUG_DETAIL = 'pharmacy_drug_detailed'; //药品详细表
    const DRUG_STORE  = 'pharmacy_drug_store';    //药品销售表
    const CART_INFO   = 'cart_info';              //购物车表


    /**
     * 获取单一药店购物车数据
     * pid:药店id
     */
    public function getcart(){
        //获取参数
        $request_data = $this->request->get();
        //验证
        $vali = $this->validate($request_data,'GetCart');
        if ($vali !== true){
            jsonResponse('400',$vali);
        }
        $pid   = $request_data['pid'];
        $uid = session('uid');
        //测试用
        // $uid   = $request_data['uid'];

        $carts = Db::name(self::CART_INFO)->where(['pid' => $pid,'uid' => $uid, 'cart_drug_status' => 1])->select()->toArray();
        $data['carts']        = $carts; //购物车数据
        $sum_count = 0; //购物车药品总件数
        foreach ($carts as $cart){
            $sum_count += $cart['cart_drug_num'];
        }
        $data['sum_count']    = $sum_count;
        jsonResponse('200','获取单一药店购物车数据成功',$data);
    }

    /**
     * 获取所有购物车数据
     * uid:用户id
     */
    public function getallcarts(){
        $uid = session('uid');
        //测试用
//         $uid   = 1;
        $where['c.cart_drug_status'] = 1; //购物车中存在
        $where['p.isvalid']          = 1; //药店存在
        $where['c.cart_drug_num']    = ['>',0];//数量大于0
        $where['c.uid']              = $uid;
        $return_data = []; //返回数据集合
        $carts = Db::name(self::CART_INFO)->alias('c')->join('pharmacy_info p','c.pid = p.id')->
                field('c.id,c.pid,c.did,c.cart_drug_name,c.cart_drug_spec,c.cart_drug_pic,c.cart_drug_price,c.cart_drug_num,p.pharmacy_name,p.id AS pharmacy_id')->
                where($where)->select()->toArray();

        if ($carts){
//            $return_data['total_num'] = count($carts); //获取所有购物车数据的数量
            array_walk($carts, function ($item,$key) use (&$return_data) {
                $item['detail_id'] = Db::name(self::DRUG_STORE)->where('id',$item['did'])->value('did'); //获取药品详细表id
                $return_data[$item['pharmacy_id']]['pharmacy_name'] = $item['pharmacy_name'];
                $return_data[$item['pharmacy_id']]['pharmacy_id']   = $item['pharmacy_id'];
                $return_data[$item['pharmacy_id']]['list'][] = $item;
            });
//            $return_data = array_values($return_data);
        }
        jsonResponse('200','获取所有购物车数据成功',$return_data);

    }

    /**
     * 添加购物车（药店页面也可以删减）
     * did:药品销售表id
     * pid:药店id
     * cart_drug_name:药品名称
     * cart_drug_spec:药品规格
     * cart_drug_pic:药品图片
     * cart_drug_price:价格（分为单位）
     * increment:1加；0减
     */
    public function addcart(){
        //获取参数
        $request_data = $this->request->post();
        //验证
        $vali = $this->validate($request_data,'AddCart');
        if ($vali !== true){
            jsonResponse('400',$vali);
        }
        //精确赋予参数
        $data['did']             = $request_data['did'];             //药品销售表id
        $data['pid']             = $request_data['pid'];             //药店id
        $data['cart_drug_name']  = $request_data['cart_drug_name'];  //药品名称
        $data['cart_drug_spec']  = $request_data['cart_drug_spec'];  //药品规格
        $data['cart_drug_pic']   = $request_data['cart_drug_pic'];   //药品图片
        $data['cart_drug_price'] = $request_data['cart_drug_price']; //药品价格
        $increment   = $request_data['increment']; //1加；0减
        $data['uid'] = session('uid');             //用户id
        //测试用
        // $data['uid'] = $request_data['uid'];
        $cart_exist = Db::name(self::CART_INFO)->where([
                                                        'uid' => $data['uid'],
                                                        'pid' => $data['pid'],
                                                        'did' => $data['did'],
                                                        'cart_drug_status' => ['>',0]])->find();
        if ($cart_exist === null && $increment == 1){
            //只有用户点击加号时，不存在此条购物车数据，需要新创建购物车
            $data['cart_drug_num'] = 1;
            $cart_insert = Db::name(self::CART_INFO)->insert($data);
            if ($cart_insert){
                jsonResponse('200','创建购物车成功');
            }else{
                jsonResponse('500','创建购物车失败');
            }
        }
        //存在此条购物车数据，针对这条数据进行更改
        $msg=''; //返回的信息
        //检查库存
        $stock = Db::name(self::DRUG_STORE)->where(['id' => $data['did'], 'isvalid' => 1])->value('drug_store_num');
        if ($stock < $cart_exist['cart_drug_num']){
            //库存小于购物车数量
            Db::name(self::CART_INFO)->where('id',$cart_exist['id'])->setField('cart_drug_num',$stock);
            jsonResponse('200','购物车数量已经大于库存，直接修改成当前库存数');
        }
        if ($increment == 1){
            //点加号
            if ($stock == $cart_exist['cart_drug_num']){
                jsonResponse('200','购物车数量等于库存，不能再增加');
            }
            Db::name(self::CART_INFO)->where('id',$cart_exist['id'])->setInc('cart_drug_num');
            $msg = '添加成功';
        }elseif($increment == 0){
            //点减号
            Db::name(self::CART_INFO)->where('id',$cart_exist['id'])->setDec('cart_drug_num');
            $msg = '删减成功';
            //当购物车数量为零时，则软删除此条数据
            $cart_drug_num = Db::name(self::CART_INFO)->where('id',$cart_exist['id'])->value('cart_drug_num');
            if ($cart_drug_num == 0){
                Db::name(self::CART_INFO)->where('id',$cart_exist['id'])->update(['cart_drug_status' => 0]);
                $msg = '数量为零，删除此条购物车数据';
            }
        }
        jsonResponse('200',$msg);
    }

    /**
     * 加减购物车数量
     * increment: 1加；0减
     * cart_id:购物车id
     * return_data:status:返回状态，0此购物车数据已经被删除或者不存在；1添加成功；2删减成功；3购物车数量已经等于库存；
     *             4数量为零，删除此条购物车数据；5购物车数量已经大于库存，直接修改成当前库存数
     *             stock:库存数量
     */
    public function onechange(){
        //获取参数
        $request_data = $this->request->post();
        //验证
        $vali = $this->validate($request_data,'One');
        if ($vali !== true){
            jsonResponse('400',$vali);
        }
        //精确赋予参数
        $increment   = $request_data['increment'];   //1加；0减
        $cart_id     = $request_data['cart_id'];     //购物车id
        //获取药店id、药店药品id、当前购物车药品数量
        $data = Db::name(self::CART_INFO)->where(['id' => $cart_id, 'cart_drug_status' => 1])->field('did,pid,cart_drug_num')->find();
        //返回状态变量
        $return_data = [];
        if (!$data){
            $return_data['status'] = 0;
            jsonResponse('400','此购物车数据已经被删除或者不存在',$return_data);
        }
        //检查库存
        $stock = Db::name(self::DRUG_STORE)->where(['id' => $data['did'], 'isvalid' => 1])->value('drug_store_num');
        if ($stock < $data['cart_drug_num']){
            //库存小于购物车数量
            Db::name(self::CART_INFO)->where('id',$cart_id)->setField('cart_drug_num',$stock);
            $return_data['status'] = 5;
            jsonResponse('200','购物车数量已经大于库存，直接修改成当前库存数',$return_data);
        }
        if ($increment == 1){
            //点加号
            if ($stock > $data['cart_drug_num']){
                //库存大于购物车数量
                Db::name(self::CART_INFO)->where('id',$cart_id)->setInc('cart_drug_num');
                $return_data['status'] = 1;
                jsonResponse('200','添加成功',$return_data);
            }elseif ($stock == $data['cart_drug_num']){
                $return_data['status'] = 3;
                //库存等于购物车数量
                jsonResponse('200','购物车数量已经等于库存',$return_data);
            }
        }elseif($increment == 0){
            //点减号
            Db::name(self::CART_INFO)->where('id',$cart_id)->setDec('cart_drug_num');
            //当购物车数量为零时，则软删除此条数据
            $cart_drug_num = Db::name(self::CART_INFO)->where('id',$cart_id)->value('cart_drug_num');
            $msg = '删减成功';
            $return_data['status'] = 2;
            if ($cart_drug_num == 0){
                Db::name(self::CART_INFO)->where('id',$cart_id)->update(['cart_drug_status' => 0]);
                $msg = '数量为零，删除此条购物车数据';
                $return_data['status'] = 4;
            }
            jsonResponse('200',$msg,$return_data);
        }
    }


    /**
     * 清空购物车
     * str_cart:购物车id字符串。例：1,2
     * pharmacy_id:药店id
     */
    public function emptycart(){
        //获取参数
        $request_data = $this->request->post();
        //验证
        $vali = $this->validate($request_data,'EmptyCart');
        if ($vali !== true){
            jsonResponse('400',$vali);
        }
        //全选清除
        $pharmacy_id = 0; //药店id（默认为0）
        $pharmacy_id = $request_data['pharmacy_id'];
        $uid         = session('uid');
        //测试用
        // $uid         = 1;

        if ($pharmacy_id !== null){
            Db::name(self::CART_INFO)->where(['pid' => $pharmacy_id, 'uid' => $uid, 'cart_drug_status' => 1])->update(['cart_drug_status' => 0]);
            jsonResponse('200','清空购物车成功');
        }
        //单选删除，传购物车的id时
        $str_cart = $request_data['str_cart'];
        $carts_id = explode(',',$str_cart);

        foreach($carts_id as $cart_id){
            Db::name(self::CART_INFO)->where(['id' => $cart_id, 'cart_drug_status' => 1])->update(['cart_drug_status' => 0]);
        }
        jsonResponse('200','清除购物车成功');
    }

}
