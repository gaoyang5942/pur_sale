<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 17:23
 */

namespace app\api\validate;


use think\Validate;

class PharmacyChoiceValidate extends Validate
{
    protected $rule = [
        'pid'             => 'require',
        'uid'             => 'require',
        'pharmacy_status' => 'require|in:2,1',
    ];

    protected $message = [
        'pid.require' => '药店id必须传',
        'uid.require' => '用户id必须传',
        'pharmacy_status.require' => '药店的选中状态必须传'
    ];

}