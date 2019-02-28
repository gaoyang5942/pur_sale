<?php
namespace app\api\model;

use think\Model;

class DrugInfo extends Model
{
    protected $table = 'yb_drug_list';

    protected $pk = 'drug_code';

    // 设置返回类型为数组
    protected $resultSetType = 'collection';

    protected $hidden = [

    ];
}