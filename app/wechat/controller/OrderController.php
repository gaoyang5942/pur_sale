<?php
namespace app\wechat\controller;

use base\controller\HomeBaseController;
use app\api\model\OrderStatus;
use app\api\model\Order;

class OrderController extends HomeBaseController
{
	public function index()
	{
		return $this->fetch();
	}

	public function self_take()
	{
		return $this->fetch();
	}

	public function wait_shipping()
	{
		return $this->fetch();
	}


	public function wait_shipping_detail()
	{
		return $this->fetch();
	}

	public function detail()
	{
		return $this->fetch();
	}

	public function wait_receive()
	{
		return $this->fetch();
	}

	public function wait_pay()
	{
		return $this->fetch();
	}

	public function all()
	{
		return $this->fetch();
	}

	public function post_sale()
	{
		return $this->fetch();
	}

	public function get_order()
	{
		return $this->fetch();
	}
}