<?php
namespace app\wechat\controller;

use base\controller\HomeBaseController;

class WeinsController extends HomeBaseController
{
	// public function pay_return()
	// {

	// }

	public function weinspay()
	{
		return $this->fetch();
	}
}