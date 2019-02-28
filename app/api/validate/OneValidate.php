<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:13
 */

namespace app\api\validate;


use think\Validate;

class OneValidate extends Validate
{
    protected $rule = [
        'increment' => 'require|in:0,1',
        'cart_id'   => 'require|number',
    ];

    protected $message = [
        'increment.require' => '必须传加减号标识',
        'cart_id.require'   => '必须传购物车id',
    ];

}