<?php
namespace app\api\model;

use think\Model;

class PharmacyDrugDetailed extends Model
{

    protected $table = 'yb_pharmacy_drug_detailed';

    public function store(){
    	return $this->hasOne('pharmacy_drug_store','did');
    }

    public function drug(){
    	return $this->hasOne('drug_info','drug_code','drug_detailed_code');
    }
}
