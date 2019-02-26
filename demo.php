<?php
class Demo{
	private $de = 11;
	public function aaa(){
		$bb = 105*1000;
		$cc = 3.53*300;
		$dd = $this->de;
		return ($bb+$cc);
	}
	public function bbb(){
		return $this->aaa();
	}
}
	$ba = new demo;
	echo $ba->bbb();
?>

