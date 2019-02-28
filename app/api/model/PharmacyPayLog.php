<?php
namespace app\api\model;

use think\Model;

class PharmacyPayLog extends Model
{
	protected $table = 'yb_pharmacy_pay_log';

	public function order()
	{
		return $this->hasOne('Order', 'drugorder_number', 'drugorder_number');
	}
}