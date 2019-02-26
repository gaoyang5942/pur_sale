<?php
class Demo{
	private $de = 11;
	protected $demo = 22;
	public function aaa(){
		$bb = 105*1000;
		$cc = 3.53*300;
		$dd = $this->de;
		return ($bb+$cc+$this->$demo);
	}
	public function bbb(){
		return $this->aaa();
	}
}
	$ba = new demo;
	echo $ba->bbb();
?>

