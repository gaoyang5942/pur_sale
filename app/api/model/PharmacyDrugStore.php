<?php
namespace app\api\model;

use think\Model;
use app\api\model\CartInfo;
use app\api\model\PharmacyDrugDetailed;

class PharmacyDrugStore extends Model
{
    protected $table = 'yb_pharmacy_drug_store';


    /*
     * 获取药品列表
     */
    public static function getList($store_id)
    {

        $list = self::alias('drug')
            ->field('drug.id,drug_store_name,drug_store_num,drug_store_manufactor,drug_store_retail_price,drug_store_activity_price,detail.drug_detailed_specifications,detail.drug_detailed_img,cate.drugtype_name,cate.id AS cate_id,did,drug.sid')
            ->join(['yb_pharmacy_drug_detailed' => 'detail'],'detail.id = drug.did','LEFT')
            ->join(['yb_pharmacy_drug_cate' => 'cate'],'cate.id = drug.cid','LEFT')
            ->where(['drug.isvalid' => 1,'drug_store_state' => 1, 'drug.sid' => $store_id])
            ->order('cate_id ASC, drug_detailed_weight ASC')
            ->select()
            ->toArray();
        $menu = [];
        if($list){
            array_walk($list,function (&$item) use (&$menu){
                if($item['cate_id']){
                    $where = ['uid'=>session('uid'),'did'=>$item['id'],'pid'=>$item['sid'],'cart_drug_status'=>'1'];
                    $cart = CartInfo::where($where)->field('id,cart_drug_num')->find();
                    $item['cart_id'] = $cart['id'];
                    $item['count'] = empty($cart['cart_drug_num'])?0:$cart['cart_drug_num'];
                    $menu[$item['cate_id']]['cate_id'] = $item['cate_id'];
                    $menu[$item['cate_id']]['cate_name'] = $item['drugtype_name'];
                    $menu[$item['cate_id']]['drugs'][] = $item;
                }
            });
        }
       return array_values($menu);
    }


    public function detail(){
        return $this->hasOne('PharmacyDrugDetailed','id','did')->field('id,drug_detailed_img,drug_detailed_specifications');
    }


    public function search($search,$sid){
        $where['drug_store_name'] = ['like', '%' . $search . '%'];
        $where['store.isvalid'] = 1;
        $where['store.sid']     = $sid;
        $data = self::alias('store')
                    ->field('cart.cart_drug_num,store.did,detail.drug_detailed_img,detail.drug_detailed_specifications,detail.drug_detailed_manufactor,store.drug_store_activity_price,store.drug_store_retail_price,store.drug_store_name,store.id,store.drug_store_num,store.sid')
                    ->join(['yb_cart_info' => 'cart'], 'store.id = cart.did and cart.cart_drug_status = 1','LEFT')
                    ->join(['yb_pharmacy_drug_detailed' => 'detail'], 'store.did = detail.id', 'LEFT')
                    ->where($where)
                    ->select()
                    ->toArray();
        return $data;
    }
}
