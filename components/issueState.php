<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 03/05/2015
 * Time: 04:17 PM
 */
namespace app\components;

use Yii;
use app\models\AppCase;
use app\components\userData;
use yii\db\Query;

class issueState {

    static function close($post)
    {
        $date = date('Y/m/d H:i:s');
        $closed_state = APP_CASE_STATUS_CLOSE;
        $id = $post['app_case_id'];
        Yii::$app->db->createCommand("UPDATE app_case SET app_case_status_id = $closed_state, updated = '$date' WHERE id = $id")->execute();
        $response = array(
            'success' => true,
        );

        $user = userData::getProfileByToken($post["token"]);
        $user_id = $user["id"];

        notification::notifyClose($id, $user_id);

        return $response;
    }

    static function reopen($post)
    {
        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        $date = date('Y/m/d H:i:s');
        $open_state = APP_CASE_STATUS_OPEN;
        $id = $post['app_case_id'];
        Yii::$app->db->createCommand("UPDATE app_case SET app_case_status_id = $open_state, updated = '$date', updated_gmt = '$gmtUpdatetimeformat' WHERE id = $id")->execute();
        $response = array(
            'success' => true,
        );

//        notification::notifyReopen($id);

        return $response;
    }

}


