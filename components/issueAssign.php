<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 04/05/2015
 * Time: 01:21 PM
 */
    namespace app\components;

    use Yii;
    use app\components\userData;
    use app\components\issueData;


    class issueAssign
    {
        static function assignFollowers($app_case_id, $users)
        {
            if(isset($app_case_id) && isset($users))
            {
                foreach($users as $user)
                {
                    $isFollowing = Yii::$app->db->createCommand("SELECT id FROM follower WHERE user_id='$user' AND app_case_id = '$app_case_id'")->execute();
                    if($isFollowing == false)
                    {
                        Yii::$app->db->createCommand("INSERT INTO follower (user_id,app_case_id) VALUES ($user,$app_case_id)")->execute();
                    }
                }

                $response = array(
                    'success' => true
                );

            }else{
                $response = array(
                    'success' => false,
                    'error' => "NO_DATA"
                );
            }
            return $response;
        }

        static function assignCreator($token, $app_case_id, $employee_number)
        {
            if(isset($app_case_id) && isset($employee_number))
            {
                $session_id = userData::getProfileByToken($token);
                $app_case = issueData::getCase($app_case_id);
                $former_owner_id = $app_case["creator_id"];
                $date = date('Y/m/d H:i:s');
                $user = userData::getProfileByEmployeeNumber($employee_number);
                $new_owner_id = $user["id"];
                $gmt_timezone = new \DateTimeZone('UTC');
                $gmtDate = new \DateTime('now', $gmt_timezone);
                $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
                Yii::$app->db->createCommand("UPDATE app_case SET creator_id = '$new_owner_id', updated = '$date', updated_gmt = '$gmtUpdatetimeformat' WHERE id = '$app_case_id'")->execute();
                $response = array(
                    'success' => true
                );

                notification::notifyAssign($session_id, $app_case_id, $new_owner_id, $former_owner_id);

            }else{
                $response = array(
                    'success' => false,
                    'error' => "NO_DATA"
                );
            }
            return $response;
        }

    }