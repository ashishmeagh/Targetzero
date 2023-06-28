<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 30/04/2015
 * Time: 04:55 PM
 */
namespace app\components;

use app\models\User;
use Yii;


class userData{

    static function getProfileByToken($token)
    {
        $user = Yii::$app->db->createCommand("SELECT u.id, u.user_name, u.first_name, u.last_name, u.email, u.role_id, r.role, d.type as device_type, Case when isnumeric(phone)<> 0 then SUBSTRING(phone, 1, 3)+ '-' +SUBSTRING(phone, 4, 3)+ '-' +SUBSTRING(phone, 7,4) Else '' End as phone, u.division, u.employee_number FROM [user] u RIGHT JOIN session s ON s.user_id = u.id join role r on u.role_id = r.id INNER JOIN device d ON d.id = s.device_id WHERE s.token = '$token'")->queryOne(); 
        return $user;
    }
    static function getProfileById($id)
    {
        $user = Yii::$app->db->createCommand("SELECT u.id, u.user_name, u.first_name, u.last_name, u.email, u.role_id, u.phone, u.division, u.employee_number, u.contractor_id FROM [user] u WHERE u.id = '$id'")->queryOne();
        return $user;
    }
    static function getProfileByEmployeeNumber($employee_number)
    {
        $user = Yii::$app->db->createCommand("SELECT id, user_name, first_name, last_name, email, role_id, phone, division, employee_number, contractor_id FROM [user] WHERE employee_number = '$employee_number'")->queryOne();
        return $user;
    }
    static function getProfileByEmployeeNumberAndJobsite($building_id, $employee_number)
    {
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building["jobsite_id"];
        $user = Yii::$app->db->createCommand("SELECT u.id, user_name, first_name, last_name, email, role_id, phone, division, employee_number, contractor_id FROM user_jobsite uj INNER JOIN [user] u ON (u.id = uj.user_id) WHERE u.employee_number = '$employee_number' AND uj.jobsite_id = '$jobsite_id'")->queryAll();
        return $user;
    }
    static function checkAssignedJobsite($building_id, $user_id)
    {
      $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
      $jobsite_id = $building["jobsite_id"];
      $assigned = Yii::$app->db->createCommand("SELECT * FROM user_jobsite WHERE user_id = '$user_id' AND jobsite_id = '$jobsite_id'")->queryOne();
      return $assigned;
    }
    static function checkAffectedUserAndJobsite($building_id, $employee_number)
    {
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building["jobsite_id"];
        $affectedUser = Yii::$app->db->createCommand("SELECT * FROM user_jobsite uj INNER JOIN [user] u ON (u.id = uj.user_id) WHERE u.employee_number = '$employee_number' AND uj.jobsite_id = '$jobsite_id'")->queryOne();
        return $affectedUser;
    }
    static function getUser($user, $pass = NULL)
    {
        if(!is_null($pass)){
            $command = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE user_name= '$user' AND password = '$pass' AND is_active = 1 AND role_id not in (10,11,12,13,14) AND IsAduser = 0 ");
        }else
        {
            $command = Yii::$app->db->createCommand( "SELECT * FROM [user] WHERE user_name= '$user' AND is_active = 1 AND role_id not in (10,11,12,13,14) AND IsAduser = 0" );
        }
        return $command->queryOne();
    }

        static function getUserbyemail($user)
    {

        $roleidnotinarray = array(10,11,12,13,14);

        $userdata = User::find()->where(["user_name" => $user, "is_active" => 1, "IsAduser" => 1])->andWhere('role_id NOT IN('.implode(',', $roleidnotinarray).')')->one();
        
        return $userdata;
    }
    static function setDefaultJobsite($token, $default_jobsite){
        $user = self::getProfileByToken($token);
        $user = User::find()->where(["id" => $user['id']])->one();
        $user->default_jobsite = (int)$default_jobsite;
        if($user->save()){
            $response = array(
                'success' => true
            );
        }else{
            $response = array(
                'success' => FALSE,
                'error' => "SET_DEFAULT_JOBSITE_ERR"
            );
        };
        return $response;
    }

    static function changePassword($token, $old_password, $new_password){
        $old_password = md5( $old_password );
        $new_password = md5( $new_password );
        $user = self::getProfileByToken($token);
        $user = userData::getUser( $user['user_name'], $old_password );
        if(!$user){
            $response = array(
                'success' => FALSE,
                'error' => "WRONG_PASSWORD"
            );
        }else{
            $command = Yii::$app->db->createCommand("UPDATE [user] SET password = '$new_password' WHERE user_name = '" . $user['user_name'] . "' AND password = '$old_password'")->execute();
            $response = array(
                'success' => true
            );
        }
        return $response;
    }
    static function resetPassword($email){
        //get user by email
        $user = User::find()->where(['email' => $email, 'is_active' => 1])->one();
        if(!$user){
            $response = array(
                'success' => FALSE,
                'error' => "WRONG_EMAIL"
            );
        }else{
            //generate random password
            $rand_pass = uniqid();
            //set password
            $user->password = md5( $rand_pass );
            if ($user->save()){
                notification::notifyRecovery( $user->id, $rand_pass );
                $response = array(
                    'success' => true
                );
            }
        }
        return $response;
    }
}
