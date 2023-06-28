<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 02/05/2015
     * Time: 04:22 PM
     */
    namespace app\components;

    use app\models\Jobsite;
    use Yii;
    use app\components\userData;
    use app\components\sessionData;

    class issueData
    {

        static function getCases( $token )
        {
            $user = userData::getProfileByToken( $token );
            $lastUpdated = sessionData::getLastUpdate( $token );
            $date = strtotime($lastUpdated);
            $date = strtotime("-60 minute", $date);
            $lastLogin =  date('Y-m-d H:i:s', $date);
            $lastLogin = "1899-12-31 23:00:00";
          
            $user_id = $user[ "id" ];
            $role = $user[ "role_id" ];
            $type_observation = APP_CASE_OBSERVATION;
            $type_incident = APP_CASE_INCIDENT;
            $type_recognition = APP_CASE_RECOGNITION;
            $type_violation = APP_CASE_VIOLATION;
            $limit = (int) CASES_LIMIT;
            $jobsites_availables = Yii::$app->db->createCommand( "SELECT * FROM user_jobsite WHERE user_id = '$user_id' " )->queryAll();
            $jobsites = array();
            foreach($jobsites_availables as $jobsite){
                $jobsites[]=$jobsite["jobsite_id"];
            }
            $jobsites = implode(",", $jobsites);
            $jobsites = "(" . $jobsites . ")";

            switch ( $role )
            {
                case 1: // admin / admin
                    $response = array(
                        'incident'    => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_incident.app_case_id,
                                                                        app_case.additional_information, app_case_incident.report_type_id, app_case_incident.report_topic_id,
                                                                        app_case_incident.incident_datetime, app_case.area_id, app_case_incident.body_part_id, app_case_incident.lost_time,
                                                                        app_case_incident.injury_type_id,app_case_incident.is_dart,app_case_incident.dart_time, app_case.creator_id, app_case.created,   app_case.updated, app_case.contractor_id, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id,app_case_incident.causation_factor,app_case_incident.is_lost_time,app_case_incident.is_property_damage,app_case_incident.recordable,  ISNULL(p.platform, '') as platform 
                                                                    FROM app_case
                                                                    INNER JOIN app_case_incident ON app_case_incident.app_case_id = app_case.id
                                                                        LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    LEFT JOIN area ON area.id = app_case.area_id
                                                                    WHERE app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_incident 
                                                                    AND app_case.updated_gmt > '$lastLogin'
                                                                     ORDER BY app_case.updated DESC" )->queryAll(),
                        'recognition' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_recognition.app_case_id,
                                                                        app_case.additional_information, app_case_recognition.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_recognition.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                        FROM app_case
                                                                        INNER JOIN app_case_recognition
                                                                        ON app_case_recognition.app_case_id = app_case.id
                                                                        LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                        WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_recognition 
                                                                        AND app_case.updated_gmt > '$lastLogin'
                                                                        ORDER BY app_case.updated DESC" )->queryAll(),
                        'violation'   => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_violation.app_case_id,
                                                                        app_case.additional_information, app_case_violation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_violation.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_violation
                                                                     ON app_case_violation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_violation 
                                                                     AND app_case.updated_gmt > '$lastLogin'ORDER BY app_case.updated DESC" )->queryAll(),
                        'observation' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_observation.app_case_id,
                                                                        app_case.additional_information, app_case_observation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_observation.correction_date, app_case.contractor_id, app_case_observation.coaching_provider, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_observation
                                                                     ON app_case_observation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_observation AND app_case.updated_gmt > '$lastLogin'ORDER BY app_case.updated DESC" )->queryAll(),
                    );
                    break;
                case 4: // executive  manager
                case 6: // system admin
                    $response = array(
                        'incident'    => Yii::$app->db->createCommand( "SELECT  TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_incident.app_case_id,
                                                                        app_case.additional_information, app_case_incident.report_type_id, app_case_incident.report_topic_id,
                                                                        app_case_incident.incident_datetime, app_case.area_id, app_case_incident.body_part_id, app_case_incident.lost_time,
                                                                        app_case_incident.injury_type_id,app_case_incident.is_dart,app_case_incident.dart_time, app_case.creator_id, app_case.created,   app_case.updated, app_case.contractor_id, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id,app_case_incident.causation_factor,app_case_incident.is_lost_time,app_case_incident.is_property_damage,app_case_incident.recordable, ISNULL(p.platform, '') as platform                                                                  
                                                                    FROM app_case
                                                                    INNER JOIN app_case_incident ON app_case_incident.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    LEFT JOIN area ON area.id = app_case.area_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_incident AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'recognition' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_recognition.app_case_id,
                                                                        app_case.additional_information, app_case_recognition.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_recognition.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                        FROM app_case
                                                                        INNER JOIN app_case_recognition
                                                                        ON app_case_recognition.app_case_id = app_case.id
                                                                        LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                        WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_recognition AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'violation'   => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_violation.app_case_id,
                                                                        app_case.additional_information, app_case_violation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_violation.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_violation
                                                                     ON app_case_violation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_violation AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'observation' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_observation.app_case_id,
                                                                        app_case.additional_information, app_case_observation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_observation.correction_date, app_case.contractor_id, app_case_observation.coaching_provider, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_observation
                                                                     ON app_case_observation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_observation AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                    );
                break;
                case 2: // WT Personnel
                case 5: // WT Project Manager
                case 3: // Safety contractor
                case 7: // WT Safety contractor
                    $response = array(
                        'incident'    => Yii::$app->db->createCommand( "SELECT TOP $limit
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_incident.app_case_id,
                                                                        app_case.additional_information, app_case_incident.report_type_id, app_case_incident.report_topic_id,
                                                                        app_case_incident.incident_datetime, app_case.area_id, app_case_incident.lost_time,
                                                                        app_case_incident.body_part_id, app_case_incident.injury_type_id, app_case.creator_id, app_case.created, 
                                                                        app_case.updated, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id,app_case_incident.causation_factor
                                                                        ,app_case_incident.is_lost_time,app_case_incident.is_dart,app_case_incident.dart_time,app_case_incident.is_property_damage,app_case_incident.recordable, ISNULL(p.platform, '') as platform    
                                                                    FROM app_case
                                                                    INNER JOIN building ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_incident ON app_case_incident.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id

                                                                    LEFT JOIN area ON area.id = app_case.area_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_incident AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'recognition' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_recognition.app_case_id,
                                                                        app_case.additional_information, app_case_recognition.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_recognition.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_recognition
                                                                        ON app_case_recognition.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_recognition AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'violation'   => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_violation.app_case_id,
                                                                        app_case.additional_information, app_case_violation.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_violation.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_violation
                                                                        ON app_case_violation.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_violation
                                                                        AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'observation' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_observation.app_case_id,
                                                                        app_case.additional_information, app_case_observation.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_observation.correction_date, app_case.contractor_id, app_case_observation.coaching_provider, 
                                                                          app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_observation
                                                                        ON app_case_observation.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_observation
                                                                        AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                    );
                    break;
                case 15://client safety personnel
                      $response = array(
                        'incident'    => Yii::$app->db->createCommand( "SELECT TOP $limit
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_incident.app_case_id,
                                                                        app_case.additional_information, app_case_incident.report_type_id, app_case_incident.report_topic_id,
                                                                        app_case_incident.incident_datetime, app_case.area_id, app_case_incident.lost_time,
                                                                        app_case_incident.body_part_id, app_case_incident.injury_type_id, app_case.creator_id, app_case.created, 
                                                                        app_case.updated, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id,app_case_incident.causation_factor
                                                                        ,app_case_incident.is_lost_time,app_case_incident.is_dart,app_case_incident.dart_time,app_case_incident.is_property_damage,app_case_incident.recordable, ISNULL(p.platform, '') as platform     
                                                                    FROM app_case
                                                                    INNER JOIN building ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_incident ON app_case_incident.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    LEFT JOIN area ON area.id = app_case.area_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_incident AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'recognition' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_recognition.app_case_id,
                                                                        app_case.additional_information, app_case_recognition.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_recognition.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_recognition
                                                                        ON app_case_recognition.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_recognition AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'violation'   => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_violation.app_case_id,
                                                                        app_case.additional_information, app_case_violation.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_violation.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_violation
                                                                        ON app_case_violation.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_violation
                                                                        AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'observation' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, building.jobsite_id,
                                                                        user_jobsite.user_id as user_jobsite_id, app_case_observation.app_case_id,
                                                                        app_case.additional_information, app_case_observation.foreman_id, app_case.creator_id, app_case.created,
                                                                          app_case.updated, app_case_observation.correction_date, app_case.contractor_id, app_case_observation.coaching_provider, 
                                                                          app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                    FROM app_case
                                                                    INNER JOIN building
                                                                        ON app_case.building_id = building.id
                                                                    INNER JOIN user_jobsite
                                                                        ON building.jobsite_id = user_jobsite.jobsite_id
                                                                    INNER JOIN app_case_observation
                                                                        ON app_case_observation.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_observation
                                                                        AND user_jobsite.user_id = '$user_id' AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                    );
                    break;
                case 16:// Trade partner
                      $response = array(
                        'incident'    => Yii::$app->db->createCommand( "SELECT  TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_incident.app_case_id,
                                                                        app_case.additional_information, app_case_incident.report_type_id, app_case_incident.report_topic_id,
                                                                        app_case_incident.incident_datetime, app_case.area_id, app_case_incident.body_part_id, app_case_incident.lost_time,
                                                                        app_case_incident.injury_type_id, app_case.creator_id, app_case.created,   app_case.updated, app_case.contractor_id, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id,app_case_incident.causation_factor
                                                                        ,app_case_incident.is_lost_time,app_case_incident.is_dart,app_case_incident.dart_time,app_case_incident.is_property_damage,app_case_incident.recordable, ISNULL(p.platform, '') as platform    
                                                                    FROM app_case
                                                                    INNER JOIN app_case_incident ON app_case_incident.app_case_id = app_case.id
                                                                    LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                    LEFT JOIN area ON area.id = app_case.area_id
                                                                    WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_incident AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'recognition' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_recognition.app_case_id,
                                                                        app_case.additional_information, app_case_recognition.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_recognition.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                        FROM app_case
                                                                        INNER JOIN app_case_recognition
                                                                        ON app_case_recognition.app_case_id = app_case.id
                                                                        LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                        WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_recognition AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'violation'   => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_violation.app_case_id,
                                                                        app_case.additional_information, app_case_violation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_violation.correction_date, app_case.contractor_id, app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_violation
                                                                     ON app_case_violation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_violation AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                        'observation' => Yii::$app->db->createCommand( "SELECT TOP $limit 
                                                                        app_case.jobsite_id, app_case.id, app_case.is_active, app_case.affected_user_id,
                                                                        app_case.app_case_type_id, app_case.app_case_status_id, app_case.app_case_sf_code_id,
                                                                        app_case.app_case_priority_id, app_case.trade_id, app_case.building_id, app_case_observation.app_case_id,
                                                                        app_case.additional_information, app_case_observation.foreman_id, app_case.creator_id, app_case.created,
                                                                        app_case.updated, app_case_observation.correction_date, app_case.contractor_id, app_case_observation.coaching_provider, 
                                                                        app_case.floor_id, app_case.sub_jobsite_id, ISNULL(p.platform, '') as platform
                                                                     FROM app_case
                                                                     INNER JOIN app_case_observation
                                                                     ON app_case_observation.app_case_id = app_case.id
                                                                     LEFT JOIN platform p ON p.id = app_case.platform_id
                                                                     WHERE  app_case.jobsite_id IN $jobsites AND app_case.app_case_type_id = $type_observation AND app_case.updated_gmt > '$lastLogin' ORDER BY app_case.updated DESC" )->queryAll(),
                    );
                    break;
                default:
                    $response = array(
                        'incident'    => array(),
                        'recognition' => array(),
                        'violation'   => array(),
                        'observation' => array()
                    );
                    break;
            }

               

            $ids = array();
            $response_timezone_aplicado = array();
            foreach ($response as $nombreTipo => $tipo){
                foreach ($tipo as $issueId => $issue){
                    if($nombreTipo == "incident"){
                        $jobsite_id = $issue["jobsite_id"];
                        $jobsite = Jobsite::find()->where("id = $jobsite_id")->one();
                        $timezone = $jobsite->timezone->timezone_code;
                        date_default_timezone_set("America/Chicago");
                        //dates in database are saved in "central time"
                        $datetime = new \DateTime($issue["incident_datetime"] ?? '');
                        $utc_time = new \DateTimeZone($timezone);
                        $datetime->setTimezone($utc_time);
                        $issue["incident_datetime"] = $datetime->format('Y-m-d H:i:s');
                    }
                    $response_timezone_aplicado[$nombreTipo][$issueId] = $issue;
                    $ids[] = $issue["creator_id"];
                    $ids[] = $issue["affected_user_id"];
                    if(isset($issue["foreman_id"])){
                        $ids[] = $issue["foreman_id"];
                    }
                }
            }
            $ids = array_values(array_unique($ids));
            $ids = "'" . implode("', '", $ids) . "'";
 
            $users_jobsites = array();
            $affected_users = Yii::$app->db->createCommand( "(SELECT id, is_active, updated, role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id, 1 as is_foreman FROM [user] WHERE role_id = 11 AND is_active = 1 and id in (SELECT uj.user_id 
                FROM user_jobsite uj WHERE uj.jobsite_id IN (SELECT uj1.jobsite_id FROM user_jobsite uj1 WHERE uj1.user_id = '" . $user_id . "')) AND updated >= '" . $lastLogin . "' ) UNION (SELECT [user].id, [user].is_active, [user].updated, [user].role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id, 0 as is_foreman FROM [user] WHERE role_id != 11 and id in (SELECT uj.user_id FROM user_jobsite uj WHERE uj.jobsite_id IN (SELECT uj1.jobsite_id FROM user_jobsite uj1 WHERE uj1.user_id = '" . $user_id . "'))AND is_active = 1 AND updated >= '" . $lastLogin . "' AND id IN ( $ids )) order by id" )->queryAll();
 
           
            foreach($affected_users as $user){
                $id = (int)$user["id"];
                $user["jobsites"] = Yii::$app->db->createCommand( "SELECT j.id, j.jobsite FROM user_jobsite uj join jobsite j on uj.jobsite_id = j.id WHERE uj.user_id = '$id'" )->queryAll();
                $users_jobsites[] = $user;
            }
            $response_timezone_aplicado["affectedUsers"] = $users_jobsites;

            return $response_timezone_aplicado;
        }

        static function getCase( $app_case_id )
        {
            $app_case = Yii::$app->db->createCommand( "SELECT ac.creator_id, ac.app_case_type_id, ac.jobsite_id, ac.updated, u.employee_number as badge, u.first_name as employee_name , u.last_name as employee_last_name, ac.affected_user_id, c.contractor as employer, t.trade, b.building, osha.code as osha, osha.description as osha_detail, ac.contractor_id as contractor, ac.additional_information as description FROM app_case ac INNER JOIN building b ON ac.building_id = b.id INNER JOIN trade t ON ac.trade_id = t.id INNER JOIN app_case_sf_code osha ON ac.app_case_sf_code_id = osha.id INNER JOIN [user] u ON ac.affected_user_id = u.id INNER JOIN contractor c ON ac.contractor_id = c.id WHERE ac.id = $app_case_id " )->queryOne();
            switch ( $app_case[ "app_case_type_id" ] )
            {
                case APP_CASE_VIOLATION:
                    $app_case_ext = Yii::$app->db->createCommand( "SELECT acv.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_violation acv LEFT JOIN [user] u ON acv.foreman_id = u.id WHERE acv.app_case_id = $app_case_id" )->queryOne();
                    break;
                case APP_CASE_RECOGNITION:
                    $app_case_ext = Yii::$app->db->createCommand( "SELECT  acr.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_recognition acr LEFT JOIN [user] u ON acr.foreman_id = u.id WHERE acr.app_case_id = $app_case_id" )->queryOne();
                    break;
                case APP_CASE_INCIDENT:
                    $app_case_incident = Yii::$app->db->createCommand( "SELECT a.area, f.floor FROM app_case ac LEFT JOIN area a ON ac.area_id = a.id INNER JOIN floor f ON ac.floor_id = f.id WHERE ac.id = $app_case_id" )->queryOne();
                    if($app_case_incident){
                        $app_case = array_merge( $app_case, $app_case_incident );
                    }
                    $app_case_ext = Yii::$app->db->createCommand( "SELECT rtype.report_type, aci.report_type_id, rtopic.report_topic, aci.incident_datetime as incident_datetime FROM app_case_incident aci INNER JOIN report_type rtype ON aci.report_type_id = rtype.id INNER JOIN report_topic rtopic ON aci.report_topic_id = rtopic.id WHERE aci.app_case_id = $app_case_id" )->queryOne();
                    break;
                case APP_CASE_OBSERVATION:
                    $app_case_ext = Yii::$app->db->createCommand( "SELECT  aco.coaching_provider, aco.correction_date, u.first_name as foreman_name, u.last_name as foreman_last_name FROM app_case_observation aco LEFT JOIN [user] u ON aco.foreman_id = u.id WHERE aco.app_case_id = $app_case_id" )->queryOne();
                    break;
            }
            $app_case = array_merge( $app_case, $app_case_ext );
            return $app_case;
        }

    }
