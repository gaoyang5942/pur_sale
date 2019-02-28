<?php
namespace app\service\service;

use think\Db;
use think\Request;
/**
 * 软删除服务
 */
class SafedelService{
    /**
     * 软删除
     * $table_name 表名
     * $table_filed 删除字段名称
     * $id 删除id
     */
    public function safe_del($table_name, $table_filed,$id){
        
        if(Db::name($table_name)->where(['id'=>$id])->update([$table_filed=>0])){
            return true;
        }else{
            return false;
        }
    }
}
