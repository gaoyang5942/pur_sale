<?php
namespace app\admin\validate;

use think\Validate;

class PharmacyInfoValidate extends Validate
{
    protected $rule = [
        'pharmacy_name' => 'require',

    ];
    protected $message = [
        'pharmacy_name.require' => '药店名称不能为空',
    ];

    protected $scene = [
        'add'  => ['pharmacy_name'],
        'edit' => ['pharmacy_name'],
    ];
}
