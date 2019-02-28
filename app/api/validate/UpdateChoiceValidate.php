<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30
 * Time: 14:09
 */

namespace app\api\validate;


use think\Validate;

class UpdateChoiceValidate extends Validate
{
    protected $rule = [
        'cart_id' => 'require',
        'status'  => 'require',
    ];

    protected $message = [
        'cart_id.require' => '必须传购物车id',
        'status.require'  => '必须传状态status'
    ];

}