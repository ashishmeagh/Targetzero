<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 29/04/2015
 * Time: 01:02 PM
 */

namespace app\components;

use app\models\Jobsite;
use Yii;
use app\models\AppCase;
use app\models\AppCaseIncident;
use app\models\AppCaseViolation;
use app\models\AppCaseRecognition;
use app\models\AppCaseObservation;
use app\components\userData;
use app\components\issueAssign;
use app\components\notification;
use yii\db\Query;
use app\models\Content;
 
class issueCreate
{

    static function createViolation( $user, $affectedUser, $post )
    {
        $transaction = Yii::$app->db->beginTransaction();
        $building_id = $post[ "building_id" ];
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building['jobsite_id'];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM jobsite WHERE id='$jobsite_id'" )->queryOne();
        $timezone_id = $jobsite['timezone_id'];
        $timezone = Yii::$app->db->createCommand( "SELECT * FROM timezone WHERE id='$timezone_id'" )->queryOne();
        $timezone_code = $timezone['timezone_code'];
        //$jobsite = Jobsite::find()->where("id = 1")->one();
        $jobsite_timezone = new \DateTimeZone($timezone_code/*$jobsite->timezone->timezone_code*/);
        //Obtener fecha y hora actual en el time zone del jobsite para los campos created y updated.
        $dateCreated = new \DateTime('now', $jobsite_timezone);
        $created = $dateCreated->format( 'Y/m/d H:i:s' );
        $updated = $created;
        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
         $isIssueexist_sql = "select * from [dbo].[app_case] ap Inner join [dbo].[app_case_violation] v on ap.id=v.app_case_id  where creator_id=".$user[ "id" ]." AND affected_user_id=". $affectedUser[ "id" ]."  AND building_id=".$post[ "building_id" ]." AND floor_id=".$post[ "floor_id" ]." AND jobsite_id=".$jobsite_id." AND app_case_type_id=".$post[ "app_case_type_id" ]." AND app_case_sf_code_id=".$post[ "app_case_sf_code_id" ]."  AND app_case_priority_id=".$post[ "app_case_priority_id" ]." AND additional_information= '". $post[ "additional_information" ]."'  AND trade_id=".$post[ "trade_id" ]." AND contractor_id=".$post[ "contractor_id" ]." AND v.correction_date='".$post[ "correction_date" ]."'";

         if(isset($post[ "area_id" ])){
                     $isIssueexist_sql .= " AND area_id=".$post[ "area_id" ];
                    }
                    if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
                    {
                        $isIssueexist_sql .= " AND sub_jobsite_id=".$post[ "subjobsite_id" ];
                    }
                    if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != "null" )
                    {
                        $isIssueexist_sql .= " AND foreman_id = ".$post[ "foreman_id" ];
                    }
                    
         $isIssueexist = Yii::$app->db->createCommand($isIssueexist_sql)->queryAll();
   
         if($isIssueexist)
         {

             $response = array(
                    'success' => FALSE,
                    'error' => "CREATE_ERR",
                    'description' => "Issue already exit",
                );

         }
         else{
       
                try
                {
                    $appCase = new AppCase();
                    $appCase->is_active = 1;
                    $appCase->created = $created;
                    $appCase->updated = $updated;
                    $appCase->updated_gmt = $gmtUpdatetimeformat;
                    $appCase->creator_id = $user[ "id" ];
                    $appCase->affected_user_id = $affectedUser[ "id" ];
                    $appCase->building_id = $post[ "building_id" ];
                    $appCase->floor_id = $post[ "floor_id" ];
                    $appCase->jobsite_id = /*$jobsite[ "jobsite_id" ]*/$jobsite_id;
                    $appCase->app_case_type_id = $post[ "app_case_type_id" ];
                   // $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;      
                    $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
                    $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
                    $appCase->additional_information = $post[ "additional_information" ];
                    $appCase->trade_id = $post[ "trade_id" ];
                    $appCase->contractor_id = $post[ "contractor_id" ];
                    if ( isset( $user[ "device_type" ] ) && $user[ "device_type" ] != "null" )
                    {
                        $appCase->platform = $user[ "device_type" ];
                    }else {
                        $appCase->platform = 'Mobile';
                    }
                    if(isset($post[ "area_id" ])){
                      $appCase->area_id = $post[ "area_id" ];
                    }
                    if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
                    {
                        $appCase->sub_jobsite_id = $post[ "subjobsite_id" ];
                    }

                    if($post[ "correction_date" ] >= date("Y-m-d"))
                    {
                       $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
                    }
                    else if($post[ "correction_date" ] < date("Y-m-d"))
                    {
                      $appCase->app_case_status_id = APP_CASE_STATUS_CLOSE; 
                    } 
                    $appCase->save();
                     //print_r($appCase->getErrors());
                     
                     
                    $lastInsertID = $appCase->id;
                    $statusTypeId = $appCase->app_case_status_id;
                    $appCaseViolation = new AppCaseViolation();
                    $appCaseViolation->app_case_id = $lastInsertID;
                    $appCaseViolation->correction_date = $post[ "correction_date" ];
                     
                    if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != "null" )
                    {
                        $appCaseViolation->foreman_id = $post[ "foreman_id" ];
                    }
                    
                    $appCaseViolation->save();
                  //  print_r($appCase->getErrors());
                        

                    $transaction->commit();
                   
                    $post[ "id" ] = $lastInsertID;
                    notification::addFollowers( $lastInsertID );
                 
                    $se_puede = jobsiteData::photoAllowed( $post[ "id" ] );
                    
                    if ( $se_puede ):
                        try
                        {
                            $response =  self::Attachment($user,$post);
                        }
                        catch ( \Exception $e )
                        {
                            header( "HTTP/1.1 200 OK" );
                            $response = array(
                                'success' => FALSE,
                                'error' => "PHOTO_UPLOAD_ERR",
                                'description' => $e,
                            );
                        }
                    else:
                        $response = array(
                            'success' => FALSE,
                            'error' => "PHOTO_UPLOAD_ERR",
                            'description' => "Photo upload not allowed in current jobsite",
                        );
                    endif;
                        
                      if($response["success"] == 1)
                      {
                          $filepath = $response["file_url"];
                      }
                   
                     if( isset($filepath) && isset($post['photo']) )
                        {
                         notification::notifyNewForMobileAttach( $lastInsertID, true, $filepath);
                       }
                       else
                       {
                        
                           notification::notifyNew($lastInsertID, true,true);
                       }
                    
                       if ( isset($post["newsflash"]) && $post["newsflash"] == "1" )
                       {
                           if( isset($filepath) && isset($post['photo']) )
                        {
                              notification::newsflashForMobileAttach( $lastInsertID, $filepath);
                        }
                        else
                        {
                            notification::newsflash($lastInsertID,true);
                        }
                       }
                       
                    $response = array(
                        'success' => TRUE,
                        'app_case_id' => $lastInsertID,
                        'statusTypeId' => $statusTypeId,
                        'affectedUser' => $affectedUser
                    );

                }
                catch ( \Exception $e )
                {
                    $transaction->rollback();
                    $response = array(
                        'success' => FALSE,
                        'error' => "CREATE_ERR",
                        'description' => $e,
                    );
                }
    }

        return $response;
    }

    static function createRecognition( $user, $affectedUser, $post )
    {
        $appCase = new AppCase;
        $appCaseRecognition = new AppCaseRecognition;

        $transaction = Yii::$app->db->beginTransaction();

        $building_id = $post[ "building_id" ];
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building['jobsite_id'];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM jobsite WHERE id='$jobsite_id'" )->queryOne();
        $timezone_id = $jobsite['timezone_id'];
        $timezone = Yii::$app->db->createCommand( "SELECT * FROM timezone WHERE id='$timezone_id'" )->queryOne();
        $timezone_code = $timezone['timezone_code'];
        //$jobsite = Jobsite::find()->where("id = 1")->one();
        $jobsite_timezone = new \DateTimeZone($timezone_code/*$jobsite->timezone->timezone_code*/);
        //Obtener fecha y hora actual en el time zone del jobiste para los campos created y updated.
        $dateCreated = new \DateTime('now', $jobsite_timezone);
        $created = $dateCreated->format( 'Y/m/d H:i:s' );
        $updated = $created;
        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );

                 $isIssueexist_sql = "select * from [dbo].[app_case] ap Inner join [dbo].[app_case_recognition] v on ap.id=v.app_case_id  where creator_id=".$user[ "id" ]." AND affected_user_id=". $affectedUser[ "id" ]."  AND building_id=".$post[ "building_id" ]." AND floor_id=".$post[ "floor_id" ]." AND jobsite_id=".$jobsite_id." AND app_case_type_id=".$post[ "app_case_type_id" ]." AND app_case_sf_code_id=".$post[ "app_case_sf_code_id" ]."  AND app_case_priority_id=".$post[ "app_case_priority_id" ]." AND additional_information= '". $post[ "additional_information" ]."'  AND trade_id=".$post[ "trade_id" ]." AND contractor_id=".$affectedUser[ "contractor_id" ]." AND v.correction_date='".$post[ "correction_date" ]."'";

                   if(isset($post[ "trade_id" ])){
                     $isIssueexist_sql .= " AND trade_id=".$post[ "trade_id" ];
                    }

                    if(isset($post[ "area_id" ])){
                     $isIssueexist_sql .= " AND area_id=".$post[ "area_id" ];
                    }
                    if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
                    {
                        $isIssueexist_sql .= " AND sub_jobsite_id=".$post[ "sub_jobsite_id" ];
                    }
                    if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != "null" )
                    {
                        $isIssueexist_sql .= " AND foreman_id = ".$post[ "foreman_id" ];
                    }
         $isIssueexist = Yii::$app->db->createCommand($isIssueexist_sql)->queryAll();

   
         if($isIssueexist)
         {

             $response = array(
                    'success' => FALSE,
                    'error' => "CREATE_ERR",
                    'description' => "Issue already exit",
                );

         }
         else{
       

        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = $created;
            $appCase->updated = $updated;
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->floor_id = $post[ "floor_id" ];
            $appCase->jobsite_id = $jobsite_id;
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_CLOSE;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $post[ "contractor_id" ];
            if ( isset( $user[ "device_type" ] ) && $user[ "device_type" ] != "null" )
            {
                $appCase->platform = $user[ "device_type" ];
            }else {
                $appCase->platform = 'Mobile';
            }
            if(isset($post[ "area_id" ])){
              $appCase->area_id = $post[ "area_id" ];
            }
            if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
            {
                $appCase->sub_jobsite_id = $post[ "subjobsite_id" ];
            }
            $appCase->save();

            $lastInsertID = $appCase->id;
            $statusTypeId = $appCase->app_case_status_id;
            $appCaseRecognition = new AppCaseRecognition();
            $appCaseRecognition->app_case_id = $lastInsertID;
            $appCaseRecognition->correction_date = $post[ "correction_date" ];
            if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != "null" )
            {
                $appCaseRecognition->foreman_id = $post[ "foreman_id" ];
            }
            $appCaseRecognition->save();

            $transaction->commit();
            $post[ "id" ] = $lastInsertID;
            notification::addFollowers( $lastInsertID );
            
             $se_puede = jobsiteData::photoAllowed( $post[ "id" ] );
            if ( $se_puede ):
                try
                {
                    $response =  self::Attachment($user,$post);
                }
                catch ( \Exception $e )
                {
                    header( "HTTP/1.1 200 OK" );
                    $response = array(
                        'success' => FALSE,
                        'error' => "PHOTO_UPLOAD_ERR",
                        'description' => $e,
                    );
                }
            else:
                $response = array(
                    'success' => FALSE,
                    'error' => "PHOTO_UPLOAD_ERR",
                    'description' => "Photo upload not allowed in current jobsite",
                );
            endif;
            
              if($response["success"] == 1)
              {
                  $filepath = $response["file_url"];
              }
           
             if( isset($filepath) && isset($post['photo']) )
                {
                 notification::notifyNewForMobileAttach( $lastInsertID, true, $filepath);
               }
               else
               {
                   notification::notifyNew($lastInsertID, true,true);
               }
            
               if ( isset($post["newsflash"]) && $post["newsflash"] == "1" )
               {
                   if( isset($filepath) && isset($post['photo']) )
                {
                      notification::newsflashForMobileAttach( $lastInsertID, $filepath);
                }
                else
                {
                    notification::newsflash($lastInsertID,true);
                }
               }


            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
                'statusTypeId' => $statusTypeId,
                'affectedUser' => $affectedUser
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }


    }


        return $response;
    }
    
    static function createIncident( $user, $affectedUser, $post )
    {
                    
        $appCase = new AppCase;
        $appCaseIncident = new AppCaseIncident;

        $transaction = Yii::$app->db->beginTransaction();
        /*Obtener jobsite a partir del building*/
        $building_id = $post[ "building_id" ];
// $message = "\nIssueCreate - DATOS RECIBIDOS";
// $message = "\nbuilding_id ingresada: $building_id";
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building['jobsite_id'];
// $message.= "\njobsite de building_id ingresada: $jobsite_id";
        if(isset($post[ "area_id" ])){
            $area_id = $post[ "area_id" ];
            $area = Yii::$app->db->createCommand( "SELECT * FROM area WHERE id='$area_id'" )->queryOne();
        }
        
/*AGREGADO PARA DEBUGGING DE LAS FECHAS*/
// $utc_timezone = new \DateTimeZone("America/Chicago");
//Obtener jobsite y time zone.
$jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
// $message.= "\njobsite: ".$jobsite->jobsite;
// $message.= "\njobsite timezone: ".$jobsite->timezone->timezone_code;
// echo "\nJobsite: ".$jobsite->jobsite;
$jobsite_timezone = new \DateTimeZone($jobsite->timezone->timezone_code);

//date_default_timezone_set($jobsite->timezone->timezone_code);
// $message.= "\njobsite timezone: $jobsite_timezone";
//Obtener fecha y hora actual en el time zone del jobiste para los campos created y updated.
$dateCreated = new \DateTime('now', $jobsite_timezone);
// $message.= "\ndate created: $dateCreated";
$created = $dateCreated->format( 'Y/m/d H:i:s' );
//$message.= "\ncreated: $created";
$updated = $created;

// echo "\nCreated: $created";
// echo "\nUpdated: $updated";
//Obtener fecha y hora de la incidencia del post (UTC +000).
//var_dump($post[ "incident_datetime" ]);
$post_incident_datetime = new \DateTime($post[ "incident_datetime" ]);
//$message.= "\npost incident datetime: ".$post_incident_datetime->format( 'Y/m/d H:i:s' );
//$temp_incident_datetime = $post_incident_datetime;
// echo "\nPOST Incident datetime: ".$post_incident_datetime->format("Y-m-d H:i:s");
//Transformar la fecha y hora de la incidencia al time zone del jobsite.
//$jobsite_incident_time = $post_incident_datetime->setTimeZone($jobsite_timezone);
//$message.= "\njobsite incident datetime: ".$jobsite_incident_time->format("Y-m-d H:i:s");

// echo "\nJobsite Incident datetime: ".$jobsite_incident_time->format("Y-m-d H:i:s");
//$datetime->setTimezone($utc_time);
//date_default_timezone_set($timezone);
//$datetime->setTimeZone(new DateTimeZone("America/Chicago"));
// echo "\nTimezone: "; var_dump($datetime->getTimezone());
// echo "\nDateTime: "; var_dump($datetime);

//dates in database are saved in "central time"
// $datetime->setTimezone($utc_timezone);
// echo "\nDateTime-UTC: $datetime";
// $datetime->format('Y-m-d H:i:s');
// echo "\nDatetime: ".date( 'Y/m/d H:i:s' );
/*FIN AGREGADO*/

        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
        try
        {   
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = $created;
            $appCase->updated = $updated;
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->jobsite_id = $jobsite->id;
            if(isset($post[ "floor_id" ])){
              $appCase->floor_id = $post[ "floor_id" ];
            };
            if(isset($post[ "area_id" ])){
              $appCase->area_id = $post[ "area_id" ];
            }
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $post[ "contractor_id" ];
            if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
            {
                $appCase->sub_jobsite_id = $post[ "subjobsite_id" ];
            }
            
            if ( isset( $user[ "device_type" ] ) && $user[ "device_type" ] != "null" )
            {
                $appCase->platform = $user[ "device_type" ];
            }else {
                $appCase->platform = 'Mobile';
            }
            
            $appCase->save();
            
            $lastInsertID = $appCase -> id;
       
            $appCaseIncident = new AppCaseIncident();
            $appCaseIncident->app_case_id = $lastInsertID;
            $appCaseIncident->report_type_id = (isset($post[ "report_type_id" ])?$post[ "report_type_id" ]:null);
            $appCaseIncident->report_topic_id = (isset($post[ "report_topic_id" ])?$post[ "report_topic_id" ]:null);
//            $appCaseIncident->incident_datetime = $post[ "incident_datetime" ];

            // $jobsite = Jobsite::find()->where("id = $appCase->jobsite_id")->one();
            // $timezone = $jobsite->timezone->timezone_code;
            // date_default_timezone_set($timezone);
            // $datetime = new \DateTime($post[ "incident_datetime" ]);
            // $utc_time = new \DateTimeZone("America/Chicago");
            // //dates in database are saved in "central time"
            // $datetime->setTimezone($utc_time);

            // $appCaseIncident->incident_datetime = $datetime->format('Y-m-d H:i:s');
            //Se registra la incident time cargado en la app (sin conversión)
//$message.= "\npost incident datetime: ".$post_incident_datetime->format( 'Y/m/d H:i:s' );
            $appCaseIncident->incident_datetime = $post_incident_datetime->format( 'Y/m/d H:i:s' );

            $appCaseIncident->recordable = (isset($post[ "recordable" ])?$post[ "recordable" ]:null);
            $appCaseIncident->lost_time = (isset($post[ "lost_time" ])?$post[ "lost_time" ]:null);
            $appCaseIncident->dart_time = (isset($post[ "dart_time" ])?$post[ "dart_time" ]:0);
            $appCaseIncident->body_part_id = (isset($post[ "body_part_id" ])?$post[ "body_part_id" ]:null);
            $appCaseIncident->injury_type_id = (isset($post[ "injury_type_id" ])?$post[ "injury_type_id" ]:null);
            $appCaseIncident->causation_factor = (isset($post[ "causation_factor" ])?$post[ "causation_factor" ]:null);
            $appCaseIncident->is_lost_time = (isset($post[ "is_lost_time" ])?$post[ "is_lost_time" ]:null);
            $appCaseIncident->is_dart = (isset($post[ "is_dart" ])?$post[ "is_dart" ]:0);
            $appCaseIncident->is_property_damage = (isset($post[ "is_property_damage" ])?$post[ "is_property_damage" ]:null);
            
            $appCaseIncident->save();
            // echo '<pre>';
            //         var_dump($appCaseIncident->errors,"Namaskar");
            //         echo '</pre>';
            //         exit('123');
//             var_dump($appCaseIncident);
//             var_dump($appCaseIncidentSaveOk);
//             echo $jobsite->id;

            $transaction->commit();
            
            $post[ "id" ] = $lastInsertID;
             notification::addFollowers( $lastInsertID );
             
            $se_puede = jobsiteData::photoAllowed( $post[ "id" ] );
            if ( $se_puede ):
                try
                {
                    $response =  self::Attachment($user,$post);
                }
                catch ( \Exception $e )
                {
                    header( "HTTP/1.1 200 OK" );
                    $response = array(
                        'success' => FALSE,
                        'error' => "PHOTO_UPLOAD_ERR",
                        'description' => $e,
                    );
                }
            else:
                $response = array(
                    'success' => FALSE,
                    'error' => "PHOTO_UPLOAD_ERR",
                    'description' => "Photo upload not allowed in current jobsite",
                );
            endif;
            
              if($response["success"] == 1)
              {
                  $filepath = $response["file_url"];
              }
           
             if( isset($filepath) && isset($post['photo']) )
                {
                 notification::notifyNewForMobileAttach( $lastInsertID, true, $filepath);
               }
               else
               {
                   notification::notifyNew($lastInsertID, true,true);
               }
            
               if ( isset($post["newsflash"]) && $post["newsflash"] == "1" )
               {
                   if( isset($filepath) && isset($post['photo']) )
                {
                      notification::newsflashForMobileAttach( $lastInsertID, $filepath);
                }
                else
                {
                    notification::newsflash($lastInsertID,true);
                }
               }
    
                //  Yii::warning($message);
                //  error_log($message, 3, "debugging-messages.log");
            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
                'affectedUser' => $affectedUser
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }

    static function createObservation( $user, $affectedUser, $post )
    {
        
        $appCase = new AppCase;
        $appCaseObservation = new AppCaseObservation;

        $transaction = Yii::$app->db->beginTransaction();

        $building_id = $post[ "building_id" ];
        $building = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $jobsite_id = $building['jobsite_id'];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM jobsite WHERE id='$jobsite_id'" )->queryOne();
        $timezone_id = $jobsite['timezone_id'];
        $timezone = Yii::$app->db->createCommand( "SELECT * FROM timezone WHERE id='$timezone_id'" )->queryOne();
        $timezone_code = $timezone['timezone_code'];
        //$jobsite = Jobsite::find()->where("id = 1")->one();
        $jobsite_timezone = new \DateTimeZone($timezone_code/*$jobsite->timezone->timezone_code*/);
        //Obtener fecha y hora actual en el time zone del jobiste para los campos created y updated.
        $dateCreated = new \DateTime('now', $jobsite_timezone);
        $created = $dateCreated->format( 'Y/m/d H:i:s' );
        $updated = $created;
        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );

                         $isIssueexist_sql = "select * from [dbo].[app_case] ap Inner join [dbo].[app_case_observation] v on ap.id=v.app_case_id  where creator_id=".$user[ "id" ]." AND affected_user_id=". $affectedUser[ "id" ]."  AND building_id=".$post[ "building_id" ]." AND floor_id=".$post[ "floor_id" ]." AND jobsite_id=".$jobsite_id." AND app_case_type_id=".$post[ "app_case_type_id" ]." AND app_case_sf_code_id=".$post[ "app_case_sf_code_id" ]."  AND app_case_priority_id=".$post[ "app_case_priority_id" ]." AND additional_information= '". $post[ "additional_information" ]."'  AND trade_id=".$post[ "trade_id" ]." AND contractor_id=".$post[ "contractor_id" ]." AND v.correction_date='".$post[ "correction_date" ]."'";
                         
                   if(isset($post[ "trade_id" ])){
                     $isIssueexist_sql .= " AND trade_id=".$post[ "trade_id" ];
                    }

                    if(isset($post[ "area_id" ])){
                     $isIssueexist_sql .= " AND area_id=".$post[ "area_id" ];
                    }
                    if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != Null )
                    {
                        $isIssueexist_sql .= " AND sub_jobsite_id=".$post[ "subjobsite_id" ];
                    }
                    if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != Null )
                    {
                        $isIssueexist_sql .= " AND v.foreman_id = ".$post[ "foreman_id" ];
                    }

                   if(isset($post[ "coaching_provider"])){
                     $isIssueexist_sql .= " AND v.coaching_provider = '" .$post[ "coaching_provider" ]."'";
                    }

                    

                    
         $isIssueexist = Yii::$app->db->createCommand($isIssueexist_sql)->queryAll();
                    
   
         if($isIssueexist)
         {

             $response = array(
                    'success' => FALSE,
                    'error' => "CREATE_ERR",
                    'description' => "Issue already exit",
                );

         }
         else{
       


        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = $created;
            $appCase->updated = $updated;
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->floor_id = $post[ "floor_id" ];
            $appCase->jobsite_id = $jobsite_id;
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $post[ "contractor_id" ];
            
            if ( isset( $user[ "device_type" ] ) && $user[ "device_type" ] != "null" )
            {
                $appCase->platform = $user[ "device_type" ];
            }else {
                $appCase->platform = 'Mobile';
            }
            if(isset($post[ "area_id" ])){
              $appCase->area_id = $post[ "area_id" ];
            }
            if ( isset( $post[ "subjobsite_id" ] ) && $post[ "subjobsite_id" ] != "null" )
            {
                $appCase->sub_jobsite_id = $post[ "subjobsite_id" ];
            }
            
            $appCase->save();
            
            $lastInsertID = $appCase->id;

            $appCaseObservation = new AppCaseObservation();
            $appCaseObservation->app_case_id = $lastInsertID;
            $appCaseObservation->correction_date = $post[ "correction_date" ];
            if ( isset( $post[ "foreman_id" ] ) && $post[ "foreman_id" ] != "null" )
            {
                $appCaseObservation->foreman_id = $post[ "foreman_id" ];
            }
            $appCaseObservation->coaching_provider = $post[ "coaching_provider" ];
            $appCaseObservation->save();
            
            $transaction->commit();
            
            $post[ "id" ] = $lastInsertID;
              notification::addFollowers( $lastInsertID );
              
               $se_puede = jobsiteData::photoAllowed( $post[ "id" ] );
            if ( $se_puede ):
                try
                {
                    $response =  self::Attachment($user,$post);
                }
                catch ( \Exception $e )
                {
                    header( "HTTP/1.1 200 OK" );
                    $response = array(
                        'success' => FALSE,
                        'error' => "PHOTO_UPLOAD_ERR",
                        'description' => $e,
                    );
                }
            else:
                $response = array(
                    'success' => FALSE,
                    'error' => "PHOTO_UPLOAD_ERR",
                    'description' => "Photo upload not allowed in current jobsite",
                );
            endif;
            
              if($response["success"] == 1)
              {
                  $filepath = $response["file_url"];
              }
           
            
             if( !empty($filepath) && isset($post['photo']) )
                {
                 notification::notifyNewForMobileAttach( $lastInsertID, true, $filepath);
               }
               else
               {
                   notification::notifyNew($lastInsertID, true,true);
               }
            
               if (isset($post["newsflash"]) && $post["newsflash"] == "1" )
               {
                   if( !empty($filepath) && isset($post['photo']) )
                {
                      notification::newsflashForMobileAttach( $lastInsertID, $filepath);
                }
                else
                {
                    notification::newsflash($lastInsertID,true);
                }
               }

               
            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
                'affectedUser' => $affectedUser
            );

        }
        catch ( \Exception $e )
        {
            
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }
 }
        return $response;
    }

    static function create( $post )
    {
        $user = userData::getProfileByToken( $post[ "token" ] );
        $affectedUsers = userData::getProfileByEmployeeNumberAndJobsite($post[ "building_id" ], $post[ "affected_user_employee_number" ] );
        //$jobsite_assigned = userData::checkAssignedJobsite( $post[ "building_id" ], $affectedUser[ "id" ] );
        $isAssignedToJobsite = userData::checkAffectedUserAndJobsite($post[ "building_id" ], $post[ "affected_user_employee_number" ]);



        if ( isset( $post[ "foreman_id" ] ) )
        {
            $foreman = userData::getProfileById( $post[ "foreman_id" ] );
        }

        if ( !$affectedUsers )
        {
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_AFF_USER_ERR",
                'description' => "The selected affected user does not exist",
            );
        }
        else if( count($affectedUsers)>1 ){
            $response = array(
                'success' => FALSE,
                'error' => "USER_JOBSITE_ERR",
                'description' => "The selected affected user is duplicated in the jobsite",
            );
        }
        else if( !$isAssignedToJobsite ){
            $response = array(
                'success' => FALSE,
                'error' => "USER_JOBSITE_ERR",
                'description' => "The selected affected user is not assigned to the selected jobsite",
            );
        }
        else if ( isset( $post[ "foreman_id" ] ) && !$foreman )
        {
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_FOREMAN_ERR",
                'description' => "The selected foreman does not exist",
            );
        }
        else
        {
            $affectedUser = $affectedUsers[0];
            $app_case_type = $post[ "app_case_type_id" ];

            try
            {
                switch ( $app_case_type )
                {
                    case APP_CASE_VIOLATION:
                        $response = self::createViolation( $user, $affectedUser, $post );
                        break;
                    case APP_CASE_RECOGNITION:
                        $response = self::createRecognition( $user, $affectedUser, $post );
                        break;
                    case APP_CASE_INCIDENT:
                        $response = self::createIncident( $user, $affectedUser, $post );
                        break;
                    case APP_CASE_OBSERVATION:
                        $response = self::createObservation( $user, $affectedUser, $post );
                        break;
                    default:
                        $response = array(
                            'success' => FALSE,
                            'error' => "CREATE_ERR",
                            'description' => "The issue type is not correct"
                        );
                }
            }
            catch ( \Exception $e )
            {
                $response = array(
                    'success' => FALSE,
                    'error' => "CREATE_ERR",
                    'description' => $e,
                );
            }
        }

        return $response;
    }
  
    static function Attachment($user,$post)
    {
         //$user = userData::getProfileByToken( $token );
         $transaction = Yii::$app->db->beginTransaction();
             try
            {
               $binary = base64_decode( $post['photo'] );
                $filename = uniqid();
                header( 'Content-Type: bitmap; charset=utf-8' );
                $file = fopen( "../web/files/" . $filename . ".jpg", 'wb' );
                fwrite( $file, $binary );
                fclose( $file );
                $content = new Content();
                  
                $content->is_active = 1;
                $content->uploader_id = $user['id'];
                $content->created = date( 'Y/m/d H:i:s' );
                $content->updated = date( 'Y/m/d H:i:s' );
                $content->app_case_id = $post['id'];
                $content->type = "jpg";
                $content->file = $filename . ".jpg";
                $content->save();
                $transaction->commit();
              //  print_r($transaction->getErrors());
                  
                $response = array(
                    'success' => TRUE,
                    'id' =>  $content->id,
                    'file_url' => "files/" . $content->file,
                    'owner_id' => $user['id'],
                );
                
            }
            catch ( \Exception $e )
            {
                $transaction->rollback();
                $response = array(
                    'success'     => FALSE,
                    'error'       => "PHOTO_UPLOAD_ERR",
                    'description' => $e,
                );
            }
            return $response;
    }
    
    /*
     EXCLUSIVO MIGRACION DE ISSUES
     */
    static function create2( $post )
    {
        $user = userData::getProfileByToken( $post[ "token" ] );
        $affectedUser = userData::getProfileByEmployeeNumber( $post[ "affected_user_employee_number" ] );
        $app_case_type = $post[ "app_case_type_id" ];
        $response = [ ];
        try
        {
            switch ( $app_case_type )
            {
                case APP_CASE_VIOLATION:
                    $response = self::createViolation2( $user, $affectedUser, $post );
                    break;
                case APP_CASE_RECOGNITION:
                    $response = self::createRecognition2( $user, $affectedUser, $post );
                    break;
                case APP_CASE_INCIDENT:
                    $response = self::createIncident2( $user, $affectedUser, $post );
                    break;
                case APP_CASE_OBSERVATION:
                    $response = self::createObservation2( $user, $affectedUser, $post );
                    break;
                default:
                    $response = array(
                        'success' => FALSE,
                        'error' => "CREATE_ERR",
                    );
            }
        }
        catch ( \Exception $e )
        {
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }

    static function createViolation2( $user, $affectedUser, $post )
    {
        $transaction = Yii::$app->db->beginTransaction();
        $building_id = $post[ "building_id" ];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();

        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = date( 'Y/m/d H:i:s' );
            $appCase->updated = date( 'Y/m/d H:i:s' );
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->floor_id = $post[ "floor_id" ];
            $appCase->jobsite_id = $jobsite[ "jobsite_id" ];
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $affectedUser[ "contractor_id" ];
            $appCase->save();

            $lastInsertID = $appCase->id;

            $appCaseViolation = new AppCaseViolation();
            $appCaseViolation->app_case_id = $lastInsertID;
            $appCaseViolation->correction_date = $post[ "correction_date" ];
            $appCaseViolation->foreman_id = $post[ "foreman_id" ];
            $appCaseViolation->save();

            $transaction->commit();

            $post[ "id" ] = $lastInsertID;

            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }

    static function createRecognition2( $user, $affectedUser, $post )
    {
        $appCase = new AppCase;
        $appCaseRecognition = new AppCaseRecognition;

        $transaction = Yii::$app->db->beginTransaction();

        $building_id = $post[ "building_id" ];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();

        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = date( 'Y/m/d H:i:s' );
            $appCase->updated = date( 'Y/m/d H:i:s' );
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->floor_id = $post[ "floor_id" ];
            $appCase->jobsite_id = $jobsite[ "jobsite_id" ];
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $affectedUser[ "contractor_id" ];
            $appCase->save();

            $lastInsertID = $appCase->id;

            $appCaseRecognition = new AppCaseRecognition();
            $appCaseRecognition->app_case_id = $lastInsertID;
            $appCaseRecognition->correction_date = $post[ "correction_date" ];
            $appCaseRecognition->foreman_id = $post[ "foreman_id" ];
            $appCaseRecognition->save();

            $transaction->commit();

            $post[ "id" ] = $lastInsertID;

            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }

    static function createIncident2( $user, $affectedUser, $post )
    {
        $appCase = new AppCase;
        $appCaseIncident = new AppCaseIncident;

        $transaction = Yii::$app->db->beginTransaction();

        $building_id = $post[ "building_id" ];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();
        $area_id = $post[ "area_id" ];
        $floor = Yii::$app->db->createCommand( "SELECT * FROM area WHERE id='$area_id'" )->queryOne();

        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = date( 'Y/m/d H:i:s' );
            $appCase->updated = date( 'Y/m/d H:i:s' );
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->jobsite_id = $jobsite[ "jobsite_id" ];
            $appCase->floor_id = $floor[ "floor_id" ];
            $appCase->area_id = $post[ "area_id" ];
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $affectedUser[ "contractor_id" ];
            $appCase->save();

            $lastInsertID = $appCase->id;

            $appCaseIncident = new AppCaseIncident();
            $appCaseIncident->app_case_id = $lastInsertID;
            $appCaseIncident->report_type_id = $post[ "report_type_id" ];
            $appCaseIncident->report_topic_id = $post[ "report_topic_id" ];
            $appCaseIncident->incident_datetime = $post[ "incident_datetime" ];
            $appCaseIncident->recordable = $post[ "recordable" ];
            $appCaseIncident->lost_time = $post[ "lost_time" ];
            $appCaseIncident->body_part_id = $post[ "body_part_id" ];
            $appCaseIncident->injury_type_id = $post[ "injury_type_id" ];
            $appCaseIncident->save();

            $transaction->commit();

            $post[ "id" ] = $lastInsertID;

            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }

    static function createObservation2( $user, $affectedUser, $post )
    {
        $appCase = new AppCase;
        $appCaseObservation = new AppCaseObservation;

        $transaction = Yii::$app->db->beginTransaction();

        $building_id = $post[ "building_id" ];
        $jobsite = Yii::$app->db->createCommand( "SELECT * FROM building WHERE id='$building_id'" )->queryOne();

        $gmt_timezone = new \DateTimeZone('UTC');
        $gmtDate = new \DateTime('now', $gmt_timezone);
        $gmtUpdatetimeformat = $gmtDate->format( 'Y/m/d H:i:s' );
        
        try
        {
            $appCase = new AppCase();
            $appCase->is_active = 1;
            $appCase->created = date( 'Y/m/d H:i:s' );
            $appCase->updated = date( 'Y/m/d H:i:s' );
            $appCase->updated_gmt = $gmtUpdatetimeformat;
            $appCase->creator_id = $user[ "id" ];
            $appCase->affected_user_id = $affectedUser[ "id" ];
            $appCase->building_id = $post[ "building_id" ];
            $appCase->floor_id = $post[ "floor_id" ];
            $appCase->jobsite_id = $jobsite[ "jobsite_id" ];
            $appCase->app_case_type_id = $post[ "app_case_type_id" ];
            $appCase->app_case_status_id = APP_CASE_STATUS_OPEN;
            $appCase->app_case_sf_code_id = $post[ "app_case_sf_code_id" ];
            $appCase->app_case_priority_id = $post[ "app_case_priority_id" ];
            $appCase->additional_information = $post[ "additional_information" ];
            $appCase->trade_id = $post[ "trade_id" ];
            $appCase->contractor_id = $affectedUser[ "contractor_id" ];
            $appCase->save();

            $lastInsertID = $appCase->id;

            $appCaseObservation = new AppCaseObservation();
            $appCaseObservation->app_case_id = $lastInsertID;
            $appCaseObservation->correction_date = $post[ "correction_date" ];
            $appCaseObservation->foreman_id = $post[ "foreman_id" ];
            $appCaseObservation->coaching_provider = $post[ "coaching_provider" ];
            $appCaseObservation->save();

            $transaction->commit();

            $post[ "id" ] = $lastInsertID;
            notification::addFollowers( $lastInsertID );

            notification::notifyNew( $lastInsertID );

            $response = array(
                'success' => TRUE,
                'app_case_id' => $lastInsertID,
            );

        }
        catch ( \Exception $e )
        {
            $transaction->rollback();
            $response = array(
                'success' => FALSE,
                'error' => "CREATE_ERR",
                'description' => $e,
            );
        }

        return $response;
    }
}
