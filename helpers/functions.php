<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 11/02/2016
 * Time: 15:28
 */

namespace app\helpers;


use app\models\ChangesTracker;
use app\models\LoginTracker;

class functions {

    static public function getIP(){
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    static public function convertFromTimezone($datetime, $fromTimezone, $toTimezone){
        $fromTimezone = new \DateTimeZone($fromTimezone);
        $toTimezone = new \DateTimeZone($toTimezone);
        $datetime = new \DateTime($datetime, $fromTimezone);
        $datetime->setTimezone($toTimezone);
        // date_default_timezone_set($fromTimezone);
        // $datetime = new \DateTime($datetime);
        // $utc_time = new \DateTimeZone($toTimezone);
        // //dates in database are saved in "central time"
        // $datetime->setTimezone($utc_time);
        // date_default_timezone_set("America/Chicago");
        return $datetime->format('Y-m-d H:i:s');
    }

    static public function trackChange($user_id, $model_id, $model_name, $field, $before, $after){
        $change = New ChangesTracker();
        $change->user_id = (int)$user_id;
        $change->timestamp = (new \DateTime())->format('Y-m-d H:i:s');//date( 'Y/m/d H:i:s' );
        $change->model_id = $model_id;
        $change->model_name = $model_name;
        $change->field_name = $field;
        $change->before_state = (string)$before;
        $change->after_state = (string)$after;
        return $change->save();
    }

    static public function trackLogin($user_id, $device, $device_id = NULL){
        $ip_address = self::getIP();
        $login = New LoginTracker();
        $login->user_id = (int)$user_id;
        $login->timestamp = date('Y/m/d H:i:s');
        $login->device = $device;
        $login->device_id = $device_id;
        $login->ip_address = $ip_address;
        return $login->save();
    }

}
