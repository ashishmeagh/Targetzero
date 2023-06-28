<?php
 
namespace app\components;

use Yii;
use app\models\User;
class AccessRule extends \yii\filters\AccessRule {
 
    /**
     * @inheritdoc
     */
    //protected function matchRole($user)
    //{
	//	return true;
    //    //if (empty($this->roles)) {
    //    //    return true;
    //    //}
	//	
	//	//var_dump($user);
	//	//exit;
	//	//if( ROLE_ADMIN == Yii::$app->session->get('user.role_id') ){
	//	//	return true;
	//	//}else{
	//	//	return false;
	//	//}
	//	
	//	
	//	
	//	//var_dump($this->roles);
	//	//exit;
	//	
    //
    //    //return false;
    //}
}