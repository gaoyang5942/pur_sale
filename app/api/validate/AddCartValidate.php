<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 15:56
 */

namespace app\api\validate;


use think\Validate;

class AddCartValidate extends Validate
{
    protected $rule = [
        'did'             => 'require|number',
        'pid'             => 'require|number',
        'cart_drug_price' => 'require',
        'increment'       => 'require|in:0,1'
    ];

}