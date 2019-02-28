<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30
 * Time: 9:21
 */

namespace app\api\validate;


use think\Validate;

class GetCartValidate extends Validate
{
    protected $rule = [
        'pid' => 'require',
    ];

    protected $message = [
      'pid.require' => '药店id必须传',
    ];

}