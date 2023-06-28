<?php

/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 06/05/2015
 * Time: 01:16 PM
 */

namespace app\components;

use app\helpers\functions;
use app\models\Jobsite;
use Yii;
use yii\db\Query;
use app\components\issueAssign;
use app\models\AppCase;
use bryglen\sendgrid;
use yii\helpers\ArrayHelper;

class notification {

   static $test_emails = ['ashish.aradala@winwire.com'];

    static function addFollowers($app_case_id) {
        $app_case = Yii::$app->db->createCommand("SELECT ac.affected_user_id, ac.building_id, ac.creator_id, ac.app_case_type_id FROM app_case ac WHERE ac.id = $app_case_id ")->queryOne();
        $affected_user_id = $app_case["affected_user_id"];
        $building_id = $app_case["building_id"];
        $app_case_type_id = $app_case["app_case_type_id"];
        $creator_id = $app_case["creator_id"];

        $contractor = Yii::$app->db->createCommand("SELECT contractor_id FROM [user] WHERE id='$affected_user_id'")->queryOne();
        $contractor_id = (int) $contractor["contractor_id"];

        $jobsite = Yii::$app->db->createCommand("SELECT jobsite_id FROM building WHERE id='$building_id'")->queryOne();
        $jobsite_id = (int) $jobsite["jobsite_id"];

        if (Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER) {
            $queryCondition = "(u.contractor_id = $contractor_id)";

            if ($app_case_type_id == APP_CASE_INCIDENT) {
               $queryCondition .= " AND (u.id != " . $affected_user_id . ")";  
            }
           
        } else {
            switch ($app_case_type_id) {
                case APP_CASE_VIOLATION:
                case APP_CASE_OBSERVATION:
                case APP_CASE_RECOGNITION:
                    //add admin to followers
                    $queryCondition = "(u.contractor_id = '148' AND u.role_id = " . ROLE_ADMIN . ")";                
                    //add affected user if is not creator
                    if ($affected_user_id != $creator_id) {
                        $queryCondition .= " OR (u.id = " . $affected_user_id . ")";
                    }
                    if ($app_case_type_id != APP_CASE_OBSERVATION) {
                        //add wt project manager to followers
                        $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id=" . ROLE_WT_PROJECT_MANAGER . ")";
                    }
                    //add contractor project manager to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_CONTRACTOR_PROJECT_MANAGER . ")";
                    //add wt safety personnel to followers
                    $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id = " . ROLE_WT_SAFETY_PERSONNEL . ")";
                    //add client safety personnel to followers
                    $queryCondition .= " OR (u.role_id = " . ROLE_CLIENT_SAFETY_PERSONNEL . ")";
                    //add contractor owner to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_CONTRACTOR_OWNER . ")";
                    //add contractor foreman to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_CONTRACTOR_FOREMAN . ")";
                    //add contractor safety manager to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_CONTRACTOR_SAFETY_MANAGER . ")";
                    //add trade partner to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_TRADE_PARTNER . ")";
                    //add safety contractor to followers
                    $queryCondition .= " OR ( u.role_id = " . ROLE_SAFETY_CONTRACTOR . ")";

                    break;

                case APP_CASE_INCIDENT:
                    //add admin to followers$contractor_id
                    $queryCondition = "(u.contractor_id = '148' AND u.role_id = " . ROLE_ADMIN . ")";
                    $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id = " . ROLE_SYSTEM_ADMIN . ")";
                    //add wt project manager to followers
                    $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id=" . ROLE_WT_PROJECT_MANAGER . ")";
                    //add wt executive manager to followers
                    $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id = " . ROLE_WT_EXECUTIVE_MANAGER . ")";
                    //add wt safety personnel to followers
                    $queryCondition .= " OR (u.contractor_id = '148' AND u.role_id = " . ROLE_WT_SAFETY_PERSONNEL . ")";
                    //add client manager to followers
                    $queryCondition .= " OR ( u.role_id = " . ROLE_CLIENT_MANAGER . ")";
                    //add client safety personnel to followers
                    $queryCondition .= " OR ( u.role_id = " . ROLE_CLIENT_SAFETY_PERSONNEL . ")";
                    //add safety contractor to followers
                    $queryCondition .= " OR ( u.role_id = " . ROLE_SAFETY_CONTRACTOR . ")";
                    //add trade partner to followers
                    $queryCondition .= " OR (u.contractor_id = $contractor_id AND u.role_id = " . ROLE_TRADE_PARTNER . ")";
                    break;
            }
        }

        $usersList = Yii::$app->db->createCommand("SELECT u.id FROM [user] u JOIN user_jobsite uj ON uj.user_id = u.id WHERE u.is_active = '1' AND uj.jobsite_id = '$jobsite_id' AND ( $queryCondition )")->queryAll();

        foreach ($usersList as $user) {
            $user_id = $user["id"];
            $isFollowing = Yii::$app->db->createCommand("SELECT id FROM follower WHERE user_id='$user_id' AND app_case_id = '$app_case_id'")->execute();
            if ($isFollowing == FALSE) {
                Yii::$app->db->createCommand("INSERT INTO follower (user_id,app_case_id) VALUES ($user_id,$app_case_id)")->execute();
            }
        }
        // add creator to follwer table
        $isFollowing = Yii::$app->db->createCommand("SELECT id FROM follower WHERE user_id='$creator_id' AND app_case_id = '$app_case_id'")->execute();
        if ($isFollowing == FALSE) {
            Yii::$app->db->createCommand("INSERT INTO follower (user_id,app_case_id) VALUES ($creator_id,$app_case_id)")->execute();
        }
        return;
    }

    public static function addCustomFollowers($app_case_id, $followersEmails)
    {

        $sqlQuery = " update app_case SET news_flash_email = '$followersEmails' where id = $app_case_id";
        Yii::$app->db->createCommand($sqlQuery)->execute();
        return;
    }

    public static function notifyIos($devicesArray, $message)
    {
        try {
            if (DEBUG == 1) {
                $passphrase = 'Julio2007';
                $certificate = '../components/cert/dev24052016/WT-prod-push.pem';
                $ssl = 'ssl://gateway.sandbox.push.apple.com:2195';
            } else if (DEBUG == 0) {
                $passphrase = 'Julio2007';
                $certificate = '../components/cert/prod24052016/WT-prod-push.pem';
                $ssl = 'ssl://gateway.push.apple.com:2195';
            }

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client($ssl, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp) {
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            }
            echo 'Connected to APNS' . PHP_EOL;

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default'
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            foreach ($devicesArray as $device) {
                $deviceToken = $device["device"];
                // Build the binary notification
                $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                // Send it to the server
                $result = fwrite($fp, $msg, strlen($msg));
            }
            fclose($fp);
        } catch (\Exception $e) {
            
        }

        return;
    }

    static function notifyAndroid($devicesArray, $message) {
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', 'AIzaSyBVG4jZ6CAXLUTU9TEJae2zodrp0jZ6k38');
        }

        $devices = array();
        foreach ($devicesArray as $device) {
            $devices[] = $device["device"];
        }
        $registrationIds = $devices;

        // Prepare the bundle
        $msg = array(
            'message' => $message,
            'title' => 'New issue',
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $msg
        );
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return;
    }

    static function notifyNew($app_case_id, $is_newsflash = NULL, $is_New = NULL, $reptoffeder) {

        $app_case = Yii::$app->db->createCommand("SELECT sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite, j.job_number, ac.creator_id, ac.affected_user_id, u2.first_name as creator_first_name, u2.last_name as creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone, ISNULL(p.platform, '') as platform
        FROM app_case ac 
		LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id 
		INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id 
		INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id 
		INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id 
		INNER JOIN contractor c ON ac.contractor_id = c.id
        LEFT JOIN platform p ON p.id = ac.platform_id 
		LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id ")->queryOne();
        
        $creator_id = $app_case["creator_id"];

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        $repeatOffendlabl = ($reptoffeder) ? '(Repeat Offender!) ':'';
                        
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new violation - ". $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Violation - ". $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Recognition - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:      
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
//                
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,aci.lost_time as lost_time,aci.dart_time as dart_time,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }       
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                               
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new observation - " . $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Observation - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }

        $jobsite = (int) $app_case["jobsite_id"];
        $app_case = array_merge($app_case, $app_case_type);
        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' ")->queryAll();

        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }
            if ($is_New == 'true') {
                $mode = 'new';
            } else {
                $mode = 'edit';
            }

            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            $app_case['reptoffendUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/repeat-offender-issues?afid=" .$app_case['affected_user_id']."&jid=".$app_case['jobsite_id'];
            $app_case['reptoffeder'] = $reptoffeder;

            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
                Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => $mode, 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("$message")
                    ->send();
            
        }
        

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id != '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';

        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id != '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

    static function notifyNewWithAttachment($app_case_id, $is_newsflash = NULL, $is_New = NULL, $reptoffeder, $destinationURLs) {
        
         $app_case = Yii::$app->db->createCommand("SELECT sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite, j.job_number, ac.creator_id, affected_user_id, u2.first_name as creator_first_name, u2.last_name as creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id ")->queryOne();
        $creator_id = $app_case["creator_id"];

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;
        $repeatOffendlabl = ($reptoffeder) ? '(Repeat Offender!) ':'';
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new violation - " . $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Violation - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new recognition - " . $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Recognition - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,aci.lost_time as lost_time,aci.dart_time as dart_time,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "A new observation - ". $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "Observation - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }

        $jobsite = (int) $app_case["jobsite_id"];
        $app_case = array_merge($app_case, $app_case_type);
        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' ")->queryAll();

        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }

        }
        

        if ($is_New == 'true') {
            $mode = 'new';
        } else {
            $mode = 'edit';
        }
        if (!empty($emails)) {
            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            $app_case['reptoffendUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/repeat-offender-issues?afid=" .$app_case['affected_user_id']."&jid=".$app_case['jobsite_id'];
            $app_case['reptoffeder'] = $reptoffeder;
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            $mail_compose = Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => $mode, 'comments' => $comments])
                ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                ->setTo($emails_arr)
                ->setSubject("$message");
                foreach ($destinationURLs as $destinationURL) {
            
                    $mail_compose->attach($destinationURL,['contentType'=>'image/jpeg']);
                }
                $mail_compose->send();
                
        }

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id != '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';

        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id != '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

       public static function newsflashNewVersion($app_case_id, $is_New = null, $selectedUsers, $customEmails, $attachments = null)
    {

        $sql = "select sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite,j.job_number, ac.creator_id, u2.first_name as creator_first_name, u2.last_name as  creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id";

        $app_case = Yii::$app->db->createCommand($sql)->queryOne();

        $jobsite_id = $app_case['jobsite_id'];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "[SAFETY ALERT] : A new violation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[SAFETY ALERT] : Violation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[SAFETY ALERT] : A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[SAFETY ALERT] : Recognition - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                 if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,aci.lost_time as lost_time,aci.dart_time as dart_time,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "[SAFETY ALERT] : A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[SAFETY ALERT] : Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[SAFETY ALERT] : A new observation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[SAFETY ALERT] : Observation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $app_case = array_merge($app_case, $app_case_type);

        $arrayCcEmails = array();
        if($customEmails != null){
            $arrayCustomEmails = array_map('trim', explode(',', $customEmails));
                foreach ($arrayCustomEmails as $CustomEmails) {
                    if ($CustomEmails != "" && !in_array($CustomEmails, $arrayCcEmails)) {
                        $arrayCcEmails[] = $CustomEmails;
                    }
                }
        }
        
         
         $arrayToEmails = array();
         if($selectedUsers != null){
         $arrayToEmails = $selectedUsers;   
       }
     
            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            
            
            $message = Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                ->setSubject("$message");
            if(empty($arrayToEmails)){
            $arrayCcEmails_new =  (IS_PRODUCTION) ? ($arrayCcEmails) : (self::$test_emails);
             $message->setTo($arrayCcEmails_new);
                 
            } else if(empty($arrayCcEmails)){
                $arrayToEmails_new =  (IS_PRODUCTION) ? ($arrayToEmails) : (self::$test_emails);
                $message->setTo($arrayToEmails_new);
                
            } else {
                $arrayToEmails_new =  (IS_PRODUCTION) ? ($arrayToEmails) : (self::$test_emails);
                $arrayCcEmails_new =  (IS_PRODUCTION) ? ($arrayCcEmails) : ([]);
                $message->setTo($arrayToEmails_new); 
                $message->setCc($arrayCcEmails_new);
                 
            }
 
        $folder = "../web/files/";
        if($attachments != null) {
            foreach ($attachments['destination_url'] as $attachValue) {
             if(!(substr($attachValue,0,5) == 'https')){
                 $message->attach($folder . $attachValue);
             }else{
                 $message->attach($attachValue);
             }   
        } 
        }
        
                       
        $message->send();
    
        $date = date('Y/m/d H:i:s');
        $usersToNotify = Yii::$app->db->createCommand("select id from [user] u where contractor_id = '1663' and is_active = '1'")->queryAll();
        $usersToNotifyArray = "";
        foreach ($usersToNotify as $user) {
            $user_id = $user["id"];
            $usersToNotifyArray .= "( '$date', '$date', '$app_case_id', '$user_id' ), ";
        }
        $usersToNotifyArray = trim($usersToNotifyArray);
        $usersToNotifyArray = trim($usersToNotifyArray, ',');
        Yii::$app->db->createCommand("INSERT INTO notification (created, updated, app_case_id, user_id ) VALUES $usersToNotifyArray")->execute();

        return;
    }

    public static function newsflash($app_case_id, $is_New = null)
    {

        $sql = "select sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite,j.job_number, ac.creator_id, u2.first_name as creator_first_name, u2.last_name as  creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id";

        $app_case = Yii::$app->db->createCommand($sql)->queryOne();

        $jobsite_id = $app_case['jobsite_id'];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new violation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Violation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Recognition - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new observation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Observation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $app_case = array_merge($app_case, $app_case_type);

        $mailsToNotify = Yii::$app->db->createCommand("SELECT news_flash_email as email FROM app_case WHERE id = '$app_case_id' AND is_active ='1' ")->queryAll();


        $mailsToNotify = Yii::$app->db->createCommand("select email from [user] u where contractor_id = '148' and is_active = '1'")->queryAll();
        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                $arrayEmails = array_map('trim', explode(',', $email["email"]));
                foreach ($arrayEmails as $Customemails) {
                    if ($Customemails != "" && !in_array($Customemails, $emails)) {
                        $emails[] = $Customemails;
                    }
                }
            }

            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("$message")
                    ->send();
            
        }

        
        $date = date('Y/m/d H:i:s');
        $usersToNotify = Yii::$app->db->createCommand("select id from [user] u where contractor_id = '1663' and is_active = '1'")->queryAll();
        $usersToNotifyArray = "";
        foreach ($usersToNotify as $user) {
            $user_id = $user["id"];
            $usersToNotifyArray .= "( '$date', '$date', '$app_case_id', '$user_id' ), ";
        }
        $usersToNotifyArray = trim($usersToNotifyArray);
        $usersToNotifyArray = trim($usersToNotifyArray, ',');
        Yii::$app->db->createCommand("INSERT INTO notification (created, updated, app_case_id, user_id ) VALUES $usersToNotifyArray")->execute();

        return;
    }

    static function newsflashWithAttachment($app_case_id, $is_New = NULL) {

        if (!empty($_FILES['attachment']["name"])) {
            $filename = $_FILES['attachment']["name"];
            $folder = "../web/files/";
            move_uploaded_file($_FILES['attachment']["tmp_name"], $folder . $filename);
        }

        $sql = "select sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite,j.job_number, ac.creator_id, u2.first_name as creator_first_name, u2.last_name as  creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id";

        $app_case = Yii::$app->db->createCommand($sql)->queryOne();

        $jobsite_id = $app_case['jobsite_id'];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new violation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Violation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Recognition - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();

                if ($is_New == 'true') {
                    $message = "[NEWSFLASH] : A new observation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                } else {
                    $message = "[NEWSFLASH] : Observation - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                }

                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $app_case = array_merge($app_case, $app_case_type);
        $mailsToNotify = Yii::$app->db->createCommand("SELECT news_flash_email as email FROM app_case WHERE id = '$app_case_id' AND is_active ='1' ")->queryAll();

        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                $arrayEmails = array_map('trim', explode(',', $email["email"]));
                foreach ($arrayEmails as $Customemails) {
                    if ($Customemails != "" && !in_array($Customemails, $emails)) {
                        $emails[] = $Customemails;
                }
            }

            }

            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];

            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);

            Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->attach($folder . $filename)
                    ->setSubject("$message")
                    ->send();
            
        }

        
        $date = date('Y/m/d H:i:s');
        $usersToNotify = Yii::$app->db->createCommand("select id from [user] u where contractor_id = '1663' and is_active = '1'")->queryAll();
        $usersToNotifyArray = "";
        foreach ($usersToNotify as $user) {
            $user_id = $user["id"];
            $usersToNotifyArray .= "( '$date', '$date', '$app_case_id', '$user_id' ), ";
        }
        $usersToNotifyArray = trim($usersToNotifyArray);
        $usersToNotifyArray = trim($usersToNotifyArray, ',');
        Yii::$app->db->createCommand("INSERT INTO notification (created, updated, app_case_id, user_id ) VALUES $usersToNotifyArray")->execute();

        return;
    }

    static function notifyNewForMobileAttach($app_case_id, $is_newsflash = NULL, $path = NULL, $reptoffeder) {
        $app_case = Yii::$app->db->createCommand("SELECT sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite, j.job_number, ac.creator_id, ac.affected_user_id, u2.first_name as creator_first_name, u2.last_name as creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id ")->queryOne();
        $creator_id = $app_case["creator_id"];

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;
        $repeatOffendlabl = ($reptoffeder) ? '(Repeat Offender!) ':'';
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $message = "A new violation - " . $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $message = "A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,aci.lost_time as lost_time,aci.dart_time as dart_time,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "A new observation - " . $repeatOffendlabl . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $jobsite = (int) $app_case["jobsite_id"];
        $app_case = array_merge($app_case, $app_case_type);


        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' ")->queryAll();


        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }
            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            $app_case['reptoffendUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/repeat-offender-issues?afid=" .$app_case['affected_user_id']."&jid=".$app_case['jobsite_id'];
            $app_case['reptoffeder'] = $reptoffeder;
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            $mail_compose = Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
		            ->setTo($emails_arr)
		            ->setSubject("$message");
            
            if(is_array($path)){
                foreach ($path as $destinationURL) {
                    $mail_compose->attach($destinationURL);
                }
            }else{
                $mail_compose->attach($path);
            }
            
            $mail_compose->send();
        
                    
        }

        

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id != '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';

        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id != '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

        static function notifyForMobileForNewAttachment($app_case_id, $is_newsflash = NULL, $path = NULL,$reptoffeder) {
        $app_case = Yii::$app->db->createCommand("SELECT sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite, j.job_number, ac.creator_id, u2.first_name as creator_first_name, u2.last_name as creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id ")->queryOne();
        $creator_id = $app_case["creator_id"];

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;
        $repeatOffendlabl = ($reptoffeder) ? '(Repeat Offender!) ':'';
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $message = "Violation - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $message = "Recognition - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();

                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();

                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);

                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,aci.lost_time as lost_time,aci.dart_time as dart_time,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "Incident - " . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {

                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;

                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "Observation - " . $repeatOffendlabl . $app_case_id . " has been updated on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $jobsite = (int) $app_case["jobsite_id"];
        $app_case = array_merge($app_case, $app_case_type);


        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' ")->queryAll();


        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }
            $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])

                    ->setTo($emails_arr)
                    ->attach($path)
                    ->setSubject("$message")
                    ->send();
            
        }

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id != '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';

        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id != '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

    static function newsflashForMobileAttach($app_case_id, $path = NULL) {

        $sql = "select sj.subjobsite, ac.id, ac.jobsite_id, j.jobsite,j.job_number, ac.creator_id, u2.first_name as creator_first_name, u2.last_name as  creator_last_name, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac LEFT JOIN sub_jobsite sj ON ac.sub_jobsite_id = sj.id INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id";

        $app_case = Yii::$app->db->createCommand($sql)->queryOne();

        $jobsite_id = $app_case['jobsite_id'];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $message = "[NEWSFLASH] : A new violation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $message = "[NEWSFLASH] : A new recognition - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                
                $app_case_type = Yii::$app->db->createCommand("SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "[NEWSFLASH] : A new incident - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "[NEWSFLASH] : A new observation - " . $app_case_id . " has been submitted on '".$app_case["jobsite"] ."' Jobsite.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $app_case = array_merge($app_case, $app_case_type);



        $mailsToNotify = Yii::$app->db->createCommand("select email from [user] u where contractor_id = '1663' and is_active = '1'")->queryAll();
        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    
                    ->setTo($emails_arr)
                    ->attach($path)
                    
                    ->setSubject("$message")
                    ->send();
            
        }

        //agrego los newsflash a las notificaciones web
        $date = date('Y/m/d H:i:s');
        $usersToNotify = Yii::$app->db->createCommand("select id from [user] u where contractor_id = '1663' and is_active = '1'")->queryAll();
        $usersToNotifyArray = "";
        foreach ($usersToNotify as $user) {
            $user_id = $user["id"];
            $usersToNotifyArray .= "( '$date', '$date', '$app_case_id', '$user_id' ), ";
        }
        $usersToNotifyArray = trim($usersToNotifyArray);
        $usersToNotifyArray = trim($usersToNotifyArray, ',');
        Yii::$app->db->createCommand("INSERT INTO notification (created, updated, app_case_id, user_id ) VALUES $usersToNotifyArray")->execute();

        return;
    }

    static function notifyAssign($session_id, $app_case_id, $assigned_user_id, $former_owner_id,$reptoffeder) {
        //check si el asignado ya esta siguiendo
        $isFollowing = Yii::$app->db->createCommand("SELECT id FROM follower WHERE user_id='$assigned_user_id' AND app_case_id = '$app_case_id'")->execute();
        if ($isFollowing == FALSE) {
            //si no esta siguiendo, lo agrego a followers
            Yii::$app->db->createCommand("INSERT INTO follower (user_id,app_case_id) VALUES ($assigned_user_id, $app_case_id)")->execute();
        }

        $app_case = Yii::$app->db->createCommand("SELECT ac.id, ac.jobsite_id, j.jobsite, j.job_number, acs.status,ac.affected_user_id, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone, u.first_name as creator_first_name, u.last_name as creator_last_name FROM app_case ac INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN contractor c ON ac.contractor_id = c.id  LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE ac.id = $app_case_id ")->queryOne();

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;
        /*
         * Notifico a los followers y no al que fue asignado
         */
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name, u.first_name as creator_first_name, u.last_name as creator_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $message = "A violation has been reassigned.";
                $message_new_owner = "A violation has been assigned to you.";
                $message_former_owner = "A violation has been assigned to you.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name, u.first_name as creator_first_name, u.last_name as creator_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $message = "A recognition has been reassigned.";
                $message_new_owner = "A recognition has been assigned to you.";
                $message_former_owner = "A recognition has been assigned to you.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                //Si no se ingresó Area, dejarla vacía y sólo agregar el floor. Hack para evitar errores después.
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT it.injury_type, rtype.report_type, aci.report_type_id, rtopic.report_topic, bp.body_part, aci.lost_time as lost_time, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id INNER JOIN body_part bp ON aci.body_part_id = bp.id INNER JOIN injury_type it ON aci.injury_type_id = it.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "An incident has been reassigned.";
                $message_new_owner = "An incident has been assigned to you.";
                $message_former_owner = "An incident has been assigned to you.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name, u.first_name as creator_first_name, u.last_name as creator_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "A observation has been reassigned.";
                $message_new_owner = "An observation has been assigned to you.";
                $message_former_owner = "An observation has been assigned to you.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }

        $app_case = array_merge($app_case, $app_case_type);

        if ($session_id != $former_owner_id):
            /*
             * Notifico al dueño anterior
             */
            $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u WHERE u.id = $former_owner_id  AND u.is_active ='1' ")->queryAll();
            if (!empty($mailsToNotify)) {
                $emails = array();
                foreach ($mailsToNotify as $email) {
                    if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                        $emails[] = $email["email"];
                    }
                }
                $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
                $app_case['reptoffendUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/repeat-offender-issues?afid=" .$app_case['affected_user_id']."&jid=".$app_case['jobsite_id'];
                $app_case['reptoffeder'] = $reptoffeder;
                $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
                Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'reassign', 'comments' => $comments])
                        ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                        ->setTo($emails_arr)
                        ->setSubject("$message")
                        ->send();
            }

            $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d WHERE d.type = 'ios' AND LEN(d.device) = 64 AND user_id = $former_owner_id")->queryAll();
            !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';

            $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d WHERE d.type = 'android' AND  d.device != 'null' AND user_id = $former_owner_id")->queryAll();
            !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        endif;

        if ($session_id != $assigned_user_id):
            /*
             * Notifico al dueño nuevo
             */
            $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u WHERE u.id = $assigned_user_id AND u.is_active ='1' ")->queryAll();
            if (!empty($mailsToNotify)) {
                $emails = array();
                foreach ($mailsToNotify as $email) {
                    if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                        $emails[] = $email["email"];
                    }
                }
                $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case['id'];
                $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
                Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'reassign', 'comments' => $comments])
                        ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                        ->setTo($emails_arr)
                        ->setSubject("$message")
                        ->send();
                

                }

            $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d WHERE d.type = 'ios' AND LEN(d.device) = 64 AND user_id = $assigned_user_id")->queryAll();
            !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message_new_owner) : '';

            $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d WHERE d.type = 'android' AND  d.device != 'null' AND user_id = $assigned_user_id")->queryAll();
            !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message_new_owner) : '';

        endif;

        return;
    }

    static function notifyClose($app_case_id, $creator_id) {
        $app_case = Yii::$app->db->createCommand("SELECT ac.jobsite_id, j.jobsite, j.job_number, acs.status, ac.app_case_type_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, u2.first_name as creator_first_name, u2.last_name as creator_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id  LEFT JOIN timezone tz ON j.timezone_id = tz.id  WHERE ac.id = $app_case_id ")->queryOne();

        $jobsite_id = $app_case["jobsite_id"];
        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
        $timezone = $jobsite->timezone->timezone_code;

        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $message = "A violation has been closed.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $message = "A recognition has been closed.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_INCIDENT:
                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                //Si no se ingresó Area, dejarla vacía y sólo agregar el floor. Hack para evitar errores después.
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }
                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT it.injury_type, rtype.report_type, aci.report_type_id, rtopic.report_topic, bp.body_part, aci.lost_time as lost_time, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id INNER JOIN body_part bp ON aci.body_part_id = bp.id INNER JOIN injury_type it ON aci.injury_type_id = it.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "An incident has been closed.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "An observation has been closed.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                $commentsTimezoned = array();
                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment["created"] = functions::convertFromTimezone($comment["created"], SERVER_TIMEZONE, $timezone);
                        $commentsTimezoned[] = $comment;
                    }
                }
                $comments = $commentsTimezoned;
                break;
        }
        $app_case = array_merge($app_case, $app_case_type);

        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' UNION SELECT email FROM [user] u WHERE u.id = '$creator_id'")->queryAll();

        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
              }

            if (isset($app_case_id)) {
                $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case_id;
            } else {
                $app_case['baseUrl'] = '';
            }
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'close', 'comments' => $comments])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                   
                    ->setTo($emails_arr)
                    ->setSubject("$message")
                    ->send();

              
        }

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id <> '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';
        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id <> '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

    static function notifyComment($app_case_id, $creator_id, $reptoffeder) {
        $app_case = Yii::$app->db->createCommand("SELECT ac.jobsite_id, j.jobsite, j.job_number, acs.status, ac.app_case_type_id, affected_user_id, ac.created, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, u2.first_name as creator_first_name, u2.last_name as creator_last_name, c.contractor as contractor_name, t.trade, j.jobsite, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description, tz.timezone FROM app_case ac INNER JOIN app_case_status acs ON ac.app_case_status_id = acs.id INNER JOIN jobsite j ON ac.jobsite_id = j.id INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN [user] u2 ON ac.creator_id = u2.id INNER JOIN contractor c ON ac.contractor_id = c.id  LEFT JOIN timezone tz ON j.timezone_id = tz.id  WHERE ac.id = $app_case_id ")->queryOne();

        
        $repeatOffendlabl = ($reptoffeder) ? '(Repeat Offender!) ':'';
        switch ($app_case["app_case_type_id"]) {
            case APP_CASE_VIOLATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id")->queryOne();
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                
                $message = "New comment on a violation report " .$repeatOffendlabl;
                break;
            case APP_CASE_RECOGNITION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id")->queryOne();
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                
                $message = "New comment on a recognition report ".$repeatOffendlabl;
                break;
            case APP_CASE_INCIDENT:

                $app_case_floor = Yii::$app->db->createCommand("SELECT f.floor FROM app_case ac INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                $app_case_incident = Yii::$app->db->createCommand("SELECT a.area, f.floor FROM app_case ac INNER JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id ")->queryOne();
                //Si no se ingresó Area, dejarla vacía y sólo agregar el floor. Hack para evitar errores después.
                if (!$app_case_incident) {
                    $app_case_incident = array("area" => '', "floor" => $app_case_floor['floor']);
                }

                $app_case = array_merge($app_case, $app_case_incident);
                $app_case_type = Yii::$app->db->createCommand("SELECT it.injury_type, rtype.report_type, aci.report_type_id, rtopic.report_topic, bp.body_part, aci.lost_time as lost_time,aci.dart_time as dart_time, aci.incident_datetime as incident_datetime,case when aci.recordable = 0 then 'NO' when aci.recordable = 1 then 'YES' end as recordable,
                case when aci.is_lost_time = 0 then 'NO' when aci.is_lost_time = 1 then 'YES' end as is_lost_time,
                case when aci.is_dart = 0 then 'NO' when aci.is_dart = 1 then 'YES' end as is_dart,
                case when aci.is_property_damage = 0 then 'NO' when aci.is_property_damage = 1 then 'YES' end as is_property_damage FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id INNER JOIN body_part bp ON aci.body_part_id = bp.id LEFT JOIN injury_type it ON aci.injury_type_id = it.id WHERE aci.app_case_id = $app_case_id")->queryOne();
                $message = "New comment on an incident report.";
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, rt.report_type, c.comment FROM comment c JOIN report_type rt ON c.report_type_id = rt.id JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                break;

            case APP_CASE_OBSERVATION:
                $app_case_type = Yii::$app->db->createCommand("SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id")->queryOne();
                $message = "New comment on an observation report ".$repeatOffendlabl;
                $comments = Yii::$app->db->createCommand("SELECT u.first_name, u.last_name, c.created, c.comment FROM comment c JOIN [user] u ON c.user_id = u.id WHERE c.app_case_id = $app_case_id")->queryAll();
                break;
        }
        if ($app_case_type) {
            $app_case = array_merge($app_case, $app_case_type);
        }

        $mailsToNotify = Yii::$app->db->createCommand("SELECT email FROM [user] u LEFT JOIN follower f ON f.user_id = u.id WHERE f.app_case_id = '$app_case_id' AND u.is_active ='1' UNION SELECT email FROM [user] u WHERE u.id = '$creator_id'")->queryAll();
        if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }

            if (!empty($emails)) {
                $app_case['baseUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/view?id=" . $app_case_id;
                $app_case['reptoffendUrl'] = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . "/app-case/repeat-offender-issues?afid=" .$app_case['affected_user_id']."&jid=".$app_case['jobsite_id'];
                $app_case['reptoffeder'] = $reptoffeder;
                $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
                Yii::$app->mailer->compose('new-issues', ['data' => $app_case, 'logo_wt' => '../mail/images/logo.png', 'from' => 'comment', 'comments' => $comments])
                        ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                        ->setTo($emails_arr)
                        ->setSubject("$message")
                        ->send();
                
            }

        }

        $iosDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'ios' AND LEN(d.device) = 64 AND d.user_id != '$creator_id'")->queryAll();
        !empty($iosDevicesToNotify) ? self::notifyIos($iosDevicesToNotify, $message) : '';
        $androidDevicesToNotify = Yii::$app->db->createCommand("SELECT device FROM device d LEFT JOIN follower f ON f.user_id = d.user_id WHERE f.app_case_id = '$app_case_id' AND d.type = 'android' AND  d.device != 'null' AND d.user_id != '$creator_id'")->queryAll();
        !empty($androidDevicesToNotify) ? self::notifyAndroid($androidDevicesToNotify, $message) : '';

        return;
    }

    static function notifyNewUser($user_id, $password) {
        $user = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE id = '$user_id'")->queryOne();
        $userJobsitesArr = Yii::$app->db->createCommand("SELECT jobsite from [jobsite] j INNER JOIN [dbo].[user_jobsite] as UJ on UJ.jobsite_id =  j.id WHERE uj.user_id = '$user_id'")->queryAll();
        $userJobsites = ArrayHelper::getColumn( $userJobsitesArr, 'jobsite' );
        $userJobsites = implode( ',', $userJobsites );
        $emails_arr =  (IS_PRODUCTION) ? ($user['email']) : (self::$test_emails);
            
            Yii::$app->mailer->compose('user-account', ['data' => $user,'jobsites' => $userJobsites, 'password' => $password, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new'])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("New account")
                    ->send();
            

        
        return;
    }

     static function notifyNewUserToAdmin($user_id, $jobsite_id) {
        $user = Yii::$app->db->createCommand("SELECT [first_name], [last_name], [employee_number], C.contractor FROM [user] INNER JOIN [dbo].[contractor] as C on C.id = [user].contractor_id  WHERE [user].id = '$user_id'")->queryOne();
      
      $sql = "SELECT Distinct email from [user] INNER JOIN [dbo].[user_jobsite] as UJ on UJ.user_id =  [user].id WHERE UJ.jobsite_id = $jobsite_id AND  [user].is_active = 1 AND [user].email != '' AND [user].role_id = 1";

    $mailsToNotify = Yii::$app->db->createCommand("SELECT Distinct email from [user] INNER JOIN [dbo].[user_jobsite] as UJ on UJ.user_id =  [user].id WHERE UJ.jobsite_id = $jobsite_id AND  [user].is_active = 1 AND [user].email != '' AND [user].role_id = 1")->queryAll();
     $jobsite_details = Yii::$app->db->createCommand("select id, jobsite,job_number from [dbo].[jobsite] where id = $jobsite_id")->queryOne();

      if (!empty($mailsToNotify)) {
            $emails = array();
            foreach ($mailsToNotify as $email) {
                if ($email["email"] != "" && !in_array($email["email"], $emails)) {
                    $emails[] = $email["email"];
                }
            }
        }

           if (!empty($emails)) {
            $emails_arr =  (IS_PRODUCTION) ? ($emails) : (self::$test_emails);
            Yii::$app->mailer->compose('user-account-info', ['data' => $user, 'logo_wt' => '../mail/images/logo.png', 'from' => 'new', 'jobsite' => $jobsite_details])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("New account")
                    ->send();
            

       }
        return;
    }

    static function notifyRecovery($user_id, $password) {

        $user = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE id = '$user_id'")->queryOne();
        if (!empty($user['email'])) {
            $emails_arr =  (IS_PRODUCTION) ? ($user['email']) : (self::$test_emails);
            Yii::$app->mailer->compose('user-account', ['data' => $user, 'password' => $password, 'logo_wt' => '../mail/images/logo.png', 'from' => 'recovery'])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("Password recovery")
                    ->send();
            
        }

        return;
    }

    static function notifyChangeUser($user_id, $password) {
        $user = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE id = '$user_id'")->queryOne();
        if (!empty($user['email'])) {
            $emails_arr =  (IS_PRODUCTION) ? ($user['email']) : (self::$test_emails);
            Yii::$app->mailer->compose('user-account', ['data' => $user, 'password' => $password, 'logo_wt' => '../mail/images/logo.png', 'from' => 'change'])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($emails_arr)
                    ->setSubject("Account updated")
                    ->send();
            

        }

        return;
    }

}
