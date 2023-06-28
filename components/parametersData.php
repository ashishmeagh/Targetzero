<?php
/**
 * Created by IntelliJ IDEA.
 * User: imilano
 * Date: 30/04/2015
 * Time: 06:44 PM
 */
namespace app\components;

use Yii;
use app\components\userData;
use app\components\sessionData;

class parametersData
{

    static function getFloors( $floors, $lastLogin )
    {
        $floor_area = array();
        foreach ( $floors as $floor )
        {
            $floor_id = (int) $floor[ "id" ];
//            $floor[ "area" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, floor_id, area FROM area WHERE floor_id = '$floor_id' AND updated >= '$lastLogin'" )->queryAll();
            $floor[ "area" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, floor_id, area FROM area WHERE floor_id = '$floor_id'" )->queryAll();
            $floor_area[ ] = $floor;
        }
        return $floor_area;
    }

    static function getBuildings( $buildings, $lastLogin )
    {
        $build_floor = array();
        foreach ( $buildings as $building )
        {
            $building_id = (int) $building[ "id" ];
//            $building[ "floor" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, building_id, floor FROM floor WHERE building_id = '$building_id' AND updated >= '$lastLogin'" )->queryAll();
            $building[ "floor" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, building_id, floor FROM floor WHERE building_id = '$building_id'" )->queryAll();
            $building[ "floor" ] = self::getFloors( $building[ "floor" ], $lastLogin );
            $build_floor[ ] = $building;
        }
        return $build_floor;
    }

    static function getJobsites( $user_id, $lastLogin )
    {
        $job_build = array();
//        $jobsites = Yii::$app->db->createCommand( "SELECT id, is_active, updated, jobsite as name, photo_allowed FROM jobsite WHERE updated >= '$lastLogin'" )->queryAll();
//        $jobsites = Yii::$app->db->createCommand( "SELECT j.id, j.is_active, j.updated, j.jobsite as name, j.photo_allowed, j.newsflash_allowed FROM user_jobsite uj JOIN jobsite j ON uj.jobsite_id = j.id WHERE user_id = '$user_id' AND updated >= '$lastLogin'" )->queryAll();
        $jobsites = Yii::$app->db->createCommand( "SELECT tz.timezone, j.id, j.is_active, j.updated, j.jobsite as name, j.photo_allowed, 0 as newsflash_allowed FROM user_jobsite uj JOIN jobsite j ON uj.jobsite_id = j.id LEFT JOIN timezone tz ON j.timezone_id = tz.id WHERE user_id = '$user_id'" )->queryAll();
        foreach ( $jobsites as $jobsite )
        {
            $id = (int) $jobsite[ "id" ];
//            $jobsite[ "building" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, building as name, description, jobsite_id FROM building WHERE jobsite_id = '$id' AND updated >= '$lastLogin'" )->queryAll();
            $jobsite[ "building" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, building as name, description, jobsite_id FROM building WHERE jobsite_id = '$id'" )->queryAll();
//            $jobsite[ "subjobsite" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, building as name, description, jobsite_id FROM building WHERE jobsite_id = '$id' AND updated >= '$lastLogin'" )->queryAll();
            $jobsite[ "subjobsite" ] = Yii::$app->db->createCommand( "SELECT id, is_active, updated, subjobsite as name, jobsite_id FROM sub_jobsite WHERE jobsite_id = '$id'" )->queryAll();
            $jobsite[ "building" ] = self::getBuildings( $jobsite[ "building" ], $lastLogin );
            $job_build[ ] = $jobsite;
        }
        return $job_build;
    }

    static function getContractors( $lastLogin,$user_id )
    {

        $contractor_jobsites = array();
        //$contractors = Yii::$app->db->createCommand( "SELECT id, is_active, updated, contractor as name, address FROM contractor WHERE  updated >= '$lastLogin'" )->queryAll();

       //$user_id = 31980;
        $contractors = Yii::$app->db->createCommand( "SELECT j.id, C.id as contractorid, C.contractor as name, j.jobsite, C.address, C.updated, C.is_active FROM contractor_jobsite cj Inner JOIN contractor C on c.id = cj.contractor_id Inner join jobsite j on cj.jobsite_id = j.id Inner Join user_jobsite uj on uj.jobsite_id = j.id  WHERE uj.[user_id] in ($user_id)  and c.updated >= '$lastLogin'group by j.id, C.contractor, j.jobsite,C.address, C.updated, C.is_active,C.id ")->queryAll();


        $contractor_added = array();
      foreach($contractors as $contractor){
         $contractor_data = array();
            
            if(!in_array($contractor["contractorid"], $contractor_added)){


             array_push($contractor_added,$contractor["contractorid"]);
           
            $contractor_data["id"] = $contractor["contractorid"];
            $contractor_data["is_active"] = $contractor["is_active"];
            $contractor_data["updated"] = $contractor["updated"];
            $contractor_data["name"] = $contractor["name"];
            $contractor_data["address"] = $contractor["address"];
            
             $contractor_jobsites_data = array();
            foreach($contractors as $contractor_jobsite){
             if($contractor_jobsite["contractorid"] ==$contractor["contractorid"] ){
            $contractor_jobsites_data["id"] = $contractor_jobsite['id'];
            $contractor_jobsites_data["jobsite"] = $contractor_jobsite["jobsite"];
            //array("id"=> (string)$contractor['id'], "jobsite"=>$contractor["jobsite"]);
            $contractor_data["jobsites"][] = $contractor_jobsites_data;
            
             }

           }
           $contractor_jobsites[] = $contractor_data;
       }
        }


     /*foreach($contractors as $contractor){
            $id = (int)$contractor["id"];
            $contractor["jobsites"] = Yii::$app->db->createCommand( "SELECT j.id, j.jobsite FROM contractor_jobsite cj join jobsite j on cj.jobsite_id = j.id WHERE cj.contractor_id = '$id' group by j.id, j.jobsite" )->queryAll();
            $contractor_jobsites[] = $contractor;
        }*/
        return $contractor_jobsites;
    }

    static function getUsers( $token )
    {
        $user = userData::getProfileByToken( $token );
        $user_id = $user["id"];
        $users_jobsites = array();
        /*
        $users = Yii::$app->db->createCommand( "SELECT u.id, is_active, updated, role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id
, CASE WHEN role_id = 11 THEN 1 ELSE 0 END as is_foreman FROM [user] u JOIN user_jobsite uj
ON uj.user_id = u.id
WHERE uj.jobsite_id IN (SELECT uj1.jobsite_id
FROM user_jobsite uj1
WHERE uj1.user_id = '$user_id') AND is_active = 1 order by u.id" )->queryAll();
        $users = Yii::$app->db->createCommand( "(SELECT id, is_active, updated, role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id, 1 as is_foreman FROM [user] WHERE role_id = 11 AND is_active = 1 ) UNION (SELECT [user].id, [user].is_active, [user].updated, [user].role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id, 0 as is_foreman FROM [user] WHERE role_id != 11  AND is_active = 1 ) order by id" )->queryAll();
        foreach($users as $user){
            $id = (int)$user["id"];
            $user["jobsites"] = Yii::$app->db->createCommand( "SELECT j.id, j.jobsite FROM user_jobsite uj join jobsite j on uj.jobsite_id = j.id WHERE uj.user_id = '$id'" )->queryAll();
            $users_jobsites[] = $user;
        }
        return $users_jobsites;
         */
         
        $lastUpdated = sessionData::getLastUpdate($token);
        if('1900-01-01 00:00:00' != $lastUpdated){

        }
        $user_jobsites_where = '';
        $users_where = '';
        if('1900-01-01 00:00:00' != $lastUpdated){
            $user_jobsites_where = " AND uj.created_at > '". $lastUpdated . "' AND uj.created_at is not null";
            $date = strtotime($lastUpdated);
            $date = strtotime("-400 minute", $date);
            $lastUpdated =  date('Y-m-d H:i:s', $date);
          
           $users_where = " AND u.updated > '".$lastUpdated. "'";
        }

           $query = "SELECT j.id, j.jobsite, uj.user_id FROM user_jobsite uj join jobsite j on uj.jobsite_id = j.id WHERE uj.jobsite_id IN (SELECT uj1.jobsite_id
            FROM user_jobsite uj1
            WHERE uj1.user_id = '$user_id')". $user_jobsites_where ;
 
        
        $response = array(
            'users_data' => Yii::$app->db->createCommand( "SELECT u.id, is_active, updated, role_id, user_name, RTRIM(LTRIM(first_name)) as first_name, RTRIM(LTRIM(last_name)) as last_name, email, phone, division,  employee_number as employee_number, contractor_id
            , CASE WHEN role_id = 11 THEN 1 ELSE 0 END as is_foreman FROM [user] u JOIN user_jobsite uj
            ON uj.user_id = u.id
            WHERE uj.jobsite_id IN (SELECT uj1.jobsite_id
            FROM user_jobsite uj1
            WHERE uj1.user_id = '$user_id') ". $users_where ." AND is_active = 1 group by u.id, is_active, updated, role_id, user_name, first_name, last_name, email, phone, division, employee_number, contractor_id" )->queryAll(),
            'user_jobsites' => Yii::$app->db->createCommand( $query )->queryAll(),
            );

        return $response;
        
    }

    static function getParameters( $token )
    {
        $user = userData::getProfileByToken( $token );
        $user_id = $user["id"];
        $lastLogin = sessionData::getLastUpdate( $token );
        $response = array(
            'default_data' => Yii::$app->db->createCommand( "SELECT default_jobsite FROM [user] WHERE id = '$user_id'" )->queryAll(),
            'report_topic' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, report_topic FROM report_topic WHERE updated >= '$lastLogin'" )->queryAll(),
            'report_type' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, report_type FROM report_type WHERE updated >= '$lastLogin'" )->queryAll(),
            'injury_type' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, injury_type FROM injury_type WHERE updated >= '$lastLogin'" )->queryAll(),
            'body_part' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, body_part FROM body_part WHERE updated >= '$lastLogin'" )->queryAll(),
            'trade' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, trade FROM trade WHERE updated >= '$lastLogin'" )->queryAll(),
            'jobsites' => self::getJobsites( $user_id, $lastLogin ),
           'contractors' => self::getContractors( $lastLogin,$user_id),
            //            'users' => self::getUsers( $lastLogin ),
            'sfCode' => Yii::$app->db->createCommand( "SELECT id, is_active, updated, code, description, parent_id FROM app_case_sf_code WHERE updated >= '$lastLogin'" )->queryAll(),
            'comments' => Yii::$app->db->createCommand( "SELECT * FROM comment WHERE updated >= '$lastLogin' ORDER BY app_case_id " )->queryAll(),
            'causationfactor_type'=>Yii::$app->db->createCommand( "SELECT * FROM causation_factor WHERE is_active = 1 ORDER BY causation_factor" )->queryAll(),
            'priority'=>Yii::$app->db->createCommand( "SELECT * FROM app_case_priority WHERE is_active = 1 order by created desc" )->queryAll(),
        );
        return $response;
    }

    static function getResources( $token )
    {
        //        $lastLogin = sessionData::getLastUpdate( $token );
        $response = array(
            'resources_type' => Yii::$app->db->createCommand( "SELECT * FROM resources_type" )->queryAll(),
            'resources' => Yii::$app->db->createCommand( "SELECT * FROM resources" )->queryAll()
        );
        return $response;
    }
}