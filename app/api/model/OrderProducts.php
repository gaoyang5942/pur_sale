<?php
namespace app\api\model;

use think\Model;

class OrderProducts extends Model
{
    protected $table = 'yb_drug_store_detailed';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    public function detail()
	{
		return $this->hasOne('PharmacyDrugDetailed', 'id', 'drugstore_code');
	}
}