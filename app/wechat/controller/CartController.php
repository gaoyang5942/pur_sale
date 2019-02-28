<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/13
 * Time: 9:19
 */

namespace app\wechat\controller;


use base\controller\HomeBaseController;

class CartController extends HomeBaseController
{
    //学习用
    public function study(){
        return $this->fetch();
    }

    //购物车页面
    public function cart(){
        return $this->fetch();
    }


}