<?php
namespace app\api\validate;

use think\Validate;

class AddressValidate extends Validate
{

	protected $rule = [
		'address_name'	=>	'require',
		'address_tel'	=>	'require',
	];

	protected $message = [
		'address_name.require'	=>	'请填写收货人姓名',
		'address_tel.require'	=>	'请填写收货人电话',
	];

	protected $scene = [
		'add'	=>	['address_name','address_tel'],
		'edit'	=>	['address_name','address_tel'],
	];
}