<?php

namespace app\components;

use Yii;
use app\models\User;
use app\models\AppCase;
use app\models\UserJobsite;
use app\models\ContractorJobsite;
use yii\helpers\ArrayHelper;

class sqlRoleBuilder{
	
	static function getJobsiteByUserId( $user_id ){
		$response = UserJobsite::find()->select('jobsite_id')->where(['user_id' => $user_id])->asArray()->all();
        if($response)
        {
            $response = ArrayHelper::getColumn( $response, 'jobsite_id' );
            $response = 'jobsite_id IN (' . implode( ',', $response ) . ')';

            return $response;
        }else{
            return "";
        }
	}
	static function getBuildingByUserId( $user_id ){
		$response = UserJobsite::find()->joinWith('jobsite')->joinWith('jobsite.buildings')->where(['user_jobsite.user_id' => $user_id])->asArray()->all();
        if($response)
        {
            $response = ArrayHelper::getColumn( $response, 'jobsite_id' );
            $response = 'jobsite_id IN (' . implode( ',', $response ) . ')';

            return $response;
        }else{
            return "";
        }
	}
	static function getFloorsByUserId( $user_id ){
		$response = UserJobsite::find()->joinWith('jobsite')->joinWith('jobsite.buildings')->joinWith('jobsite.buildings.floors')->where(['user_jobsite.user_id' => $user_id])->asArray()->all();
        if($response)
        {
            $response = ArrayHelper::getColumn( $response, 'jobsite.id' );
            $response = 'jobsite_id IN (' . implode( ',', $response ) . ')';

            return $response;
        }else{
            return "";
        }
	}
	static function getContractorsByJobsiteId( $jobsite_id ){
        
    if ($jobsite_id != ""){
        
		$response = ContractorJobsite::find()->where($jobsite_id)->asArray()->all();
        }else{
            
            $response = ContractorJobsite::find()->asArray()->all();
        }
		
               
        if($response)
        {
            $response = ArrayHelper::getColumn( $response, 'contractor_id' );
            $response = 'Id IN (' . implode( ',', $response ) . ')';
            return $response;
        }else{
            return "";
        }
	}

        static function getContractorsByJobsiteIdinusersearch( $jobsite_id ){
        
    if ($jobsite_id != ""){
        
        $response = ContractorJobsite::find()->select('contractor_id')->distinct()->where($jobsite_id)->asArray()->all();
        }else{
            
            $response = ContractorJobsite::find()->select('contractor_id')->distinct()->asArray()->all();
        }
        
               
        if($response)
        {
            $response = ArrayHelper::getColumn( $response, 'contractor_id' );
            $response = implode( ',', $response );
            return $response;
        }else{
            return "";
        }
    }
      /*  static function getUsersByJobsites($jobsites)
        {
            $response = UserJobsite::find()->select('user_id')->where($jobsites)->groupBy('user_id')->asArray()->all();
            if($response)
            {
                $response = ArrayHelper::getColumn($response, 'user_id');
                $response = '[user].id IN ('.implode(',', $response).')';
                return $response;
            }
            else
            {
                return "";
            }
        }*/
		 static function getUsersByJobsites($jobsites)
        {
			 $loggedInUser  = Yii::$app->session->get( "user.id" );
			 $value  = strtoupper($loggedInUser[0]);
           $response = UserJobsite::find()->select('user_id')->where('jobsite_id in (select jobsite_id from user_jobsite where user_id = '.$value.' )')->groupBy('user_id')->asArray()->all();
           return $response;
            /*  if($response)
            {
                $response = ArrayHelper::getColumn($response, 'user_id');
                $response = '[user].id IN ('.implode(',', $response).')';
                return $response;
            }
            else
            {
                return "";
            } */
        }

    static function getIsContractorsValid( $jobsite_id, $contractorid ){
      $IsContractorsValid = Yii::$app->db->createCommand( "select count(*) as count from [dbo].[contractor_jobsite] where contractor_id = $contractorid". $jobsite_id )->queryAll();  
      if($IsContractorsValid[0]['count'] != "0"){
           return true;
      }else{
        return false;
      }
    
        
        
    }
}

?>