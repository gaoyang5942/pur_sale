<?php
namespace app\api\model;

use think\Model;

class PharmacyDrug extends Model
{
    protected $table = 'yb_pharmacy_drug_store';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];

    public function detail()
    {
        return $this->hasOne('app\api\model\DrugInfo', 'drug_code', 'drug_store_code', [], 'LEFT');
    }

    public function store()
    {
        return $this->hasOne('app\api\model\PharmacyInfo', 'id', 'pid');
    }

    public function drugdetail()
    {
        return $this->hasOne('app\api\model\PharmacyDrugDetailed', 'id', 'did', [], 'LEFT');
    }
}