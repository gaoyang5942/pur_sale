<?php
namespace app\admin\controller;

use base\controller\AdminBaseController;
use think\Request;
use think\Db;

/**
 * 日志管理
 */
class LogController extends AdminBaseController{

    /**
     * 登录日志列表
     */
    public function login_index(){

        $keywords    = $this->request->param('keywords');
        $start_time  = $this->request->param('start_time');
        $end_time    = $this->request->param('end_time');
        $map['l.type'] = ['=', 1];
        if ($keywords != '') {
            $map['l.uid|u.user_login'] = ['like', '%'.$keywords.'%'];
        }
        if(!empty($start_time)){
            $map['l.create_time'] = ['>=', strtotime($start_time)];
        }

        if(!empty($end_time)){
            $map['l.end_time'] = ['<=', strtotime($end_time)];
        }
        $list = Db::name('system_log')->alias('l')->join('user u ','l.uid= u.id')->field('l.id, l.uid, u.user_login, u.last_login_ip, l.create_time')->where($map)->order('l.id desc')->paginate(10)->each(function($item, $key){
                $item['username'] = $item['user_login'];
                $item['ip']       = $item['last_login_ip'];
                return $item;
            });
        //保存查询条件
        $list->appends(['keywords' => $keywords, 'start_time' => $start_time, 'end_time' => $end_time]);

        // 获取分页显示
        $page = $list->render();

        $this->assign("page", $page);
        $this->assign("list", $list);
        return $this->fetch();
    }

    /**
     * 操作日志列表
     */
    public function action_index(){

        $keywords   = $this->request->param('keywords');
        $start_time = $this->request->param('start_time');
        $end_time   = $this->request->param('end_time');
        $map['l.type'] = ['=', 2];
        if ($keywords != '') {
            $map['l.uid|u.user_login'] = ['like', '%'.$keywords.'%'];
        }

        if(!empty($start_time)){
            $map['l.create_time'] = ['>=', strtotime($start_time)];
        }

        if(!empty($end_time)){
            $map['l.end_time'] = ['<=', strtotime($end_time)];
        }

        $list = Db::name('system_log')->alias('l')->join('user u ','l.uid= u.id')->field('l.id, l.uid, u.user_login, l.description, l.module, l.action, l.controller, l.create_time')->where($map)->order('l.id desc')->paginate(10)->each(function($item, $key){
                $item['username'] = $item['user_login'];
                return $item;
            });

        //保存查询条件
         $list->appends(['keywords' => $keywords, 'start_time' => $start_time, 'end_time' => $end_time]);

        // 获取分页显示
        $page = $list->render();

        $this->assign("page", $page);
        $this->assign("list", $list);
        return $this->fetch();
    }

    /**
     * 缴费日志列表
     */
    public function pay_index(){

        $keywords   = $this->request->param('keywords');
        $start_time = $this->request->param('start_time');
        $end_time   = $this->request->param('end_time');

        $map['l.type'] = ['=', 3];
        if ($keywords != ''){
            $map['l.description&o.out_trade_no'] = ['like', '%'.$keywords.'%'];
        }

        if(!empty($start_time)){
            $map['l.create_time'] = ['>=', strtotime($start_time)];
        }

        if(!empty($end_time)){
            $map['l.end_time'] = ['<=', strtotime($end_time)];
        }

        $list = Db::name('system_log')->alias('l')->join('pay_health_suborder s ','l.description= s.out_trade_no')->join('pay_health_order o ','s.hid= o.id')->field('l.id, l.description, o.out_trade_no, s.total_fee, s.update_time')->where($map)->paginate(10)->each(function($item, $key){
            $item['price'] = sprintf("%.2f",$item['total_fee'] / 100);
            $item['time']  = date('Y-m-d H:i:s', $item['create_time']);
            return $item;
        });

        //保存查询条件
        $list->appends(['keywords' => $keywords, 'start_time' => $start_time, 'end_time' => $end_time]);

        // 获取分页显示
        $page = $list->render();

        $this->assign("page", $page);
        $this->assign("list", $list);
        return $this->fetch();
    }

    /**
     * 操作日志列表
     */
    public function rec_index(){

        $start_time = $this->request->param('start_time');
        $end_time   = $this->request->param('end_time');
        $map['l.type'] = ['=', 4];
        if(!empty($start_time)){
            $map['l.create_time'] = ['>=', strtotime($start_time)];
        }
        if(!empty($end_time)){
            $map['l.end_time'] = ['<=', strtotime($end_time)];
        }
        $list = Db::name('system_log')->where($map)->order("id DESC")->paginate(10);

        //保存查询条件
        $list->appends(['uid' => $uid]);

        // 获取分页显示
        $page = $list->render();

        $this->assign("page", $page);
        $this->assign("list", $list);
        return $this->fetch();
    }

}
