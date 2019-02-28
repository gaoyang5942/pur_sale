<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 8:54
 */

namespace app\api\validate;


use think\Validate;

class EmptyCartValidate extends Validate
{
    protected $rule = [
//        'str_cart'    => 'HaveComma',
        'pharmacy_id' => 'number',
    ];

    protected $message = [
        'str_cart.require' => '购物车id字符串必须传'
    ];

    //判断str_cart是否含有逗号
//    protected function HaveComma($value,$rule){
//        if (strpos($value,',') !== false){
//            //存在逗号
//            return true;
//        }else{
//            //不存在逗号
//            return false;
//        }
//    }

}