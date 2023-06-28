<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 10/12/2015
 * Time: 14:43
 */

namespace app\helpers;

use app\models\Role;
use app\models\searches\Contractor;
use app\models\UserJobsite;
use Yii;
use yii\helpers\ArrayHelper;

class security {

    static public function getAvailableRoles($UserRole = null, $NewCreateUser = null){
   $current_role = Yii::$app->session->get('user.role_id');
        $current_id = Yii::$app->session->get('user.id');
        $roles = ArrayHelper::map( Role::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'role' );
        $filtered_roles = array();
        switch($current_role){
            case ROLE_ADMIN:
                foreach ( $roles as $key => $value ){
                    if(in_array( $key, $GLOBALS[ 'jobsite_admin' ] ) )
                    {
                        if(($current_role == ROLE_ADMIN) && ($UserRole != ROLE_ADMIN ))
                        {
                         if ($key != ROLE_ADMIN)   
                           $filtered_roles[$key] = $roles[ $key ];
                        }else if($NewCreateUser) {
                            if ($key != ROLE_ADMIN)   
                           $filtered_roles[$key] = $roles[ $key ];
                        }else{
                            $filtered_roles[$key] = $roles[ $key ];
                        }
                    }
                }
                break;
            case ROLE_SYSTEM_ADMIN:
                $filtered_roles = $roles;
                break;
            case ROLE_TRADE_PARTNER:
                foreach ( $roles as $key => $value ){
                    if(in_array( $key, $GLOBALS[ 'contractor_roles' ] ) )
                    {
                        $filtered_roles[$key] = $roles[ $key ];
                    }
                }
                break;
        }

        return $filtered_roles;
    }
    static public function getContractorRoles(){
        $roles = ArrayHelper::map( Role::find()->where([ "is_active" => 1 ])->asArray()->all(), 'id', 'role' );
        $filtered_roles = array();
        foreach($roles as $key=>$value){
            if(in_array($key, $GLOBALS['contractor_roles'])){
                $filtered_roles[$key] = $roles[ $key ];
            }
        }

        return $filtered_roles;
    }
    static public function getContractors(){
        $contractors = null;
        $userId = Yii::$app->session->get('user.id');
        $available_jobsites = Yii::$app->db->createCommand("exec [dbo].[AvailableUserJobsites] '".$userId."'")->queryAll();

        $jobsites = array();
        if($available_jobsites){
            foreach($available_jobsites as $jobsite){
                
                   $jobsites[] = $jobsite["jobsite_id"];
            }

            $jobsites = implode(",", $jobsites);
            $contractors_sp = Yii::$app->db->createCommand("exec [dbo].[JobsiteContractors] '".$jobsites."'")->queryAll();
            $contractors = ArrayHelper::map( $contractors_sp, 'id', 'contractor' );
            
        }
        return $contractors;
    }

}