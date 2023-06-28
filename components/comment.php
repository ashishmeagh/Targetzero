<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 27/05/2015
 * Time: 04:50 PM
 */
namespace app\components;

use Yii;
use app\components\issueData;
use app\models\Jobsite;
use app\models\searches\AppCase as AppCaseSearch;

class comment{

    static function createComment($token, $app_case_id, $report_type_id, $comment)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $app_case = issueData::getCase($app_case_id);
            $user = userData::getProfileByToken($token);
            $app_case_type_id = $app_case["app_case_type_id"];
            $comment_id = '';

$jobsite = Jobsite::find()->where("id = ".$app_case['jobsite_id'])->one();
$timezone_id = $jobsite['timezone_id'];
$timezone = Yii::$app->db->createCommand( "SELECT * FROM timezone WHERE id='$timezone_id'" )->queryOne();
$timezone_code = $timezone['timezone_code'];
$jobsite_timezone = new \DateTimeZone($timezone_code);
//$jobsite_timezone = new \DateTimeZone($jobsite->timezone->timezone_code);
//Obtener fecha y hora actual en el time zone del jobiste para los campos created y updated.
$dateCreated = new \DateTime('now', $jobsite_timezone);
$created = $dateCreated->format( 'Y/m/d H:i:s' );

$reptoffeder = false;
$searchModel = new AppCaseSearch();
$offenderuserandissues = $searchModel->CheckRepeatoffenderissues($app_case['affected_user_id'],$app_case['jobsite_id']);
$reptoffeder = (count($offenderuserandissues) > 0) ? true: false;

            $gmt_timezone = new \DateTimeZone('UTC');
            $gmtDate = new \DateTime('now', $gmt_timezone);
            $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
            
            switch($app_case_type_id)
            {
                case APP_CASE_VIOLATION: //violation
                case APP_CASE_RECOGNITION: //recognition
                case APP_CASE_OBSERVATION: //observation
                    Yii::$app->db->createCommand()
                        ->insert('comment', [
                            'is_active' => true,
                            'created' => $created,
                            'updated' => $gmtUpdatetimeformat,
                            'user_id' => $user["id"],
                            'app_case_id' => $app_case_id,
                            'comment' => $comment,
                            'causation_factor'=> NULL
                        ])
                        ->execute();
                    $comment_id = Yii::$app->db->getLastInsertID();
                    break;
                case APP_CASE_INCIDENT: //incident
                    Yii::$app->db->createCommand()
                        ->insert('comment', [
                            'is_active' => true,
                            'created' => $created,
                            'updated' => $gmtUpdatetimeformat,
                            'user_id' => $user["id"],
                            'app_case_id' => $app_case_id,
                            'report_type_id' => $report_type_id,
                            'comment' => $comment,
                            'causation_factor'=> NULL
                        ])
                        ->execute();

                    $comment_id = Yii::$app->db->getLastInsertID();
                    Yii::$app->db->createCommand("UPDATE app_case_incident SET report_type_id = '$report_type_id' WHERE app_case_id = $app_case_id")->execute();
                    $status_closed = APP_CASE_STATUS_CLOSE;
                    $status_open = APP_CASE_STATUS_OPEN;
                    $report_type_id == APP_CASE_INCIDENT_FINAL ? Yii::$app->db->createCommand("UPDATE app_case SET app_case_status_id = $status_closed, updated = '$created' WHERE id = $app_case_id")->execute() : Yii::$app->db->createCommand("UPDATE app_case SET app_case_status_id = $status_open, updated = '$created' WHERE id = $app_case_id")->execute() ;
                    break;
            }
            Yii::$app->db->createCommand("UPDATE app_case SET updated = '$created', updated_gmt = '$gmtUpdatetimeformat' WHERE id = $app_case_id")->execute();
            $transaction->commit();

            notification::notifyComment($app_case_id, $user["id"], $reptoffeder);
            $response = array(
                'success' => true,
                'comment_id' => $comment_id
            );
        } catch(\Exception $e){
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error'   => "CREATE_COMMENT_ERR",
            );
        }
        return $response;
    }
}
