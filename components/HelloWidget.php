<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class HelloWidget extends Widget{
	
	public $message;
	
	public function init(){
		
		parent::init();
		
		if($this->message === null){
			
			$this->message = Html::tag('input', '', ['class'=>'form-control incident-date-picker']);
			
		}else{
			
			$this->message = 'Welcome '.$this->message;
			
		}
	}
	
	public function run(){
		
		return $this->message;
		
	}
}
?>
