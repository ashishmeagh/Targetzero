<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\AppCaseSfCode;
use kartik\select2\Select2;
?>
<style>
    .filepond--root {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial,
            sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    }

    .filepond--root {
        font-size: 20px;
    }

    .filepond--credits {
        display: none;
    }

    .field-attachment {
        width: 445px;
    }

    .attachment-checkbox {
        margin-bottom: 15px;
    }

    .filepond--root .filepond--drop-label {
        min-height: 4em;
    }

    .filepond-error {
        color: #f44336;
        margin-bottom: 5px;
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>

<?php
   //ONly For Loading the Draft data

         $userid = Yii::$app->session->get("user.id");

             $sqlQuery = "select formdata from [dbo].[app_case_draft] where typeid = $model_master->app_case_type_id AND userid = $userid";
       
            $data = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
            $draftversion = false;
            $draftattachment = 0;
            if(isset($data[0]["formdata"]) && ($model_master->isNewRecord) ){
             $draftversion = true;
            $myJSON = json_decode($data[0]["formdata"], true);

            
          if(isset($myJSON["AppCase"]["jobsite_id"]) && $myJSON["AppCase"]["jobsite_id"] != ""){
              $model_master->jobsite_id = $myJSON["AppCase"]["jobsite_id"];
              $jobsite = Yii::$app->db->createCommand("SELECT photo_allowed FROM jobsite WHERE jobsite.id =  $model_master->jobsite_id")->queryOne();
              $draftattachment = $jobsite["photo_allowed"];
          }
           if(isset($myJSON["AppCase"]["sub_jobsite_id"]) && $myJSON["AppCase"]["sub_jobsite_id"] != "")
              $model_master->sub_jobsite_id = $myJSON["AppCase"]["sub_jobsite_id"];


           if(isset($myJSON["AppCase"]["contractor_id"]) && $myJSON["AppCase"]["contractor_id"] != "")
              $model_master->contractor_id = $myJSON["AppCase"]["contractor_id"];
           if(isset($myJSON["AppCase"]["affected_user_id"]) && $myJSON["AppCase"]["affected_user_id"] != "")
              $model_master->affected_user_id = $myJSON["AppCase"]["affected_user_id"];

          if(isset($myJSON["AppCase"]["building_id"]) && $myJSON["AppCase"]["building_id"] != "")
              $model_master->building_id = $myJSON["AppCase"]["building_id"];

          if(isset($myJSON["AppCase"]["floor_id"]) && $myJSON["AppCase"]["floor_id"] != "")
              $model_master->floor_id = $myJSON["AppCase"]["floor_id"];

          if(isset($myJSON["AppCase"]["area_id"]) && $myJSON["AppCase"]["area_id"] != "")
              $model_master->affected_user_id = $myJSON["AppCase"]["area_id"];
    if (APP_CASE_RECOGNITION != $model_master->app_case_type_id) {
          if(isset($myJSON["AppCase"]["app_case_status_id"]) && $myJSON["AppCase"]["app_case_status_id"] != "")
              $model_master->app_case_status_id = $myJSON["AppCase"]["app_case_status_id"];
      }

    if(isset($myJSON["AppCase"]["app_case_sf_code_id"]) && $myJSON["AppCase"]["app_case_sf_code_id"] != "")
              $model_master->app_case_sf_code_id = $myJSON["AppCase"]["app_case_sf_code_id"];

    if(isset($myJSON["AppCase"]["app_case_priority_id"]) && $myJSON["AppCase"]["app_case_priority_id"] != "")
              $model_master->app_case_priority_id = $myJSON["AppCase"]["app_case_priority_id"];
                    
    if(isset($myJSON["AppCase"]["trade_id"]) && $myJSON["AppCase"]["trade_id"] != "")
              $model_master->trade_id = $myJSON["AppCase"]["trade_id"];
                    if(isset($myJSON["AppCase"]["additional_information"]) && $myJSON["AppCase"]["additional_information"] != "")
              $model_master->additional_information = $myJSON["AppCase"]["additional_information"];
            
  if(isset($myJSON["AppCase"]["is_active"]) && $myJSON["AppCase"]["is_active"] != "")
              $model_master->is_active = $myJSON["AppCase"]["is_active"];
            
    if(isset($myJSON["AppCase"]["additional_information"]) && $myJSON["AppCase"]["additional_information"] != "")
              $model_master->additional_information = $myJSON["AppCase"]["additional_information"];


        // App Case Violation
        if (APP_CASE_VIOLATION == $model_master->app_case_type_id) {
            if(isset($myJSON["AppCaseViolation"]["foreman_id"]) && $myJSON["AppCaseViolation"]
            ["foreman_id"] != "")
            $model_detail->foreman_id = $myJSON["AppCaseViolation"]
            ["foreman_id"];
    if(isset($myJSON["AppCaseViolation"]["correction_date"]) && $myJSON["AppCaseViolation"]
            ["correction_date"] != "")
            $model_detail->correction_date = $myJSON["AppCaseViolation"]
            ["correction_date"];

        }

        // App Case Recognition
        if (APP_CASE_RECOGNITION == $model_master->app_case_type_id) {
             if(isset($myJSON["AppCaseRecognition"]["foreman_id"]) && $myJSON["AppCaseRecognition"]
            ["foreman_id"] != "")
            $model_detail->foreman_id = $myJSON["AppCaseRecognition"]
            ["foreman_id"];
        }

        // App Case Observation
        if (APP_CASE_OBSERVATION == $model_master->app_case_type_id) {

            if(isset($myJSON["AppCaseObservation"]["foreman_id"]) && $myJSON["AppCaseObservation"]
            ["foreman_id"] != "")
            $model_detail->foreman_id = $myJSON["AppCaseObservation"]
            ["foreman_id"];

            if(isset($myJSON["AppCaseObservation"]["coaching_provider"]) && $myJSON["AppCaseObservation"]
            ["coaching_provider"] != "")
            $model_detail->coaching_provider = $myJSON["AppCaseObservation"]
            ["coaching_provider"];
            
        if (isset($myJSON["AppCaseObservation"]["correction_date"]) && $myJSON["AppCaseObservation"]["correction_date"] != "")
            $model_detail->correction_date = $myJSON["AppCaseObservation"]["correction_date"];
        }

        // App Case Incident
        if (APP_CASE_INCIDENT == $model_master->app_case_type_id) {
            if(isset($myJSON["AppCaseIncident"]["report_topic_id"]) && $myJSON["AppCaseIncident"]
            ["report_topic_id"] != "")
            $model_detail->report_topic_id = $myJSON["AppCaseIncident"]
            ["report_topic_id"];
            if(isset($myJSON["AppCaseIncident"]["report_type_id"]) && $myJSON["AppCaseIncident"]
            ["report_type_id"] != "")
            $model_detail->report_type_id = $myJSON["AppCaseIncident"]
            ["report_type_id"];

            if(isset($myJSON["AppCaseIncident"]["causation_factor"]) && $myJSON["AppCaseIncident"]
            ["causation_factor"] != "")
            $model_detail->causation_factor = $myJSON["AppCaseIncident"]
            ["causation_factor"];

            if(isset($myJSON["AppCaseIncident"]["body_part_id"]) && $myJSON["AppCaseIncident"]
            ["body_part_id"] != "")
            $model_detail->body_part_id = $myJSON["AppCaseIncident"]
            ["body_part_id"];

            if(isset($myJSON["AppCaseIncident"]["injury_type_id"]) && $myJSON["AppCaseIncident"]
            ["injury_type_id"] != "")
            $model_detail->injury_type_id = $myJSON["AppCaseIncident"]
            ["injury_type_id"];

            if(isset($myJSON["AppCaseIncident"]["incident_time"]) && $myJSON["AppCaseIncident"]
            ["incident_time"] != "")
            $model_detail->incident_time = $myJSON["AppCaseIncident"]
            ["incident_time"];

            if(isset($myJSON["AppCaseIncident"]["incident_date"]) && $myJSON["AppCaseIncident"]
            ["incident_date"] != "")
            $model_detail->incident_date = $myJSON["AppCaseIncident"]
            ["incident_date"];

            if(isset($myJSON["AppCaseIncident"]["is_lost_time"]) && $myJSON["AppCaseIncident"]
            ["is_lost_time"] != "")
            $model_detail->is_lost_time = $myJSON["AppCaseIncident"]
            ["is_lost_time"];

            if(isset($myJSON["AppCaseIncident"]
            ["recordable"]) &&  $myJSON["AppCaseIncident"]
            ["recordable"]!= "")
            $model_detail->recordable = $myJSON["AppCaseIncident"]
            ["recordable"];

                        if(isset($myJSON["AppCaseIncident"]["lost_time"]) && $myJSON["AppCaseIncident"]
            ["lost_time"] != "")
            $model_detail->lost_time = $myJSON["AppCaseIncident"]
            ["lost_time"];

                        if(isset($myJSON["AppCaseIncident"]["is_property_damage"]) && $myJSON["AppCaseIncident"]
            ["is_property_damage"] != "")
            $model_detail->is_property_damage = $myJSON["AppCaseIncident"]
            ["is_property_damage"];

            if(isset($myJSON["AppCaseIncident"]["is_dart"]) && $myJSON["AppCaseIncident"]
            ["is_dart"] != "")
            $model_detail->is_dart = $myJSON["AppCaseIncident"]
            ["is_dart"];

            if(isset($myJSON["AppCaseIncident"]["recordable"]) && $myJSON["AppCaseIncident"]
            ["recordable"] != "")
            $model_detail->recordable = $myJSON["AppCaseIncident"]
            ["recordable"];

            if(isset($myJSON["AppCaseIncident"]["dart_time"]) && $myJSON["AppCaseIncident"]
            ["dart_time"] != "")
            $model_detail->dart_time = $myJSON["AppCaseIncident"]
            ["dart_time"];
        }

     
            }            

//get available jobsites for current user
$data_jobsite = ArrayHelper::map(app\models\Jobsite::find()->joinWith('userJobsites')->where(["jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get("user.id")])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite');

//get user and role
$user = app\models\User::find()->where([
            "id" => Yii::$app->session->get("user.id")
        ])->one();
$userRole = $user->role_id;
//config which roles are allowed to skip notifications.
$skipNotificationAllowedRoles = array(ROLE_SYSTEM_ADMIN, ROLE_ADMIN);
$canSkipNotifications = isset($modeUpdate) && in_array($userRole, $skipNotificationAllowedRoles);


//init dropdowns
// Affected User
if (is_numeric($model_master->jobsite_id) && is_numeric($model_master->contractor_id)):
    $data_affected_user = app\models\User::find()->joinWith('userJobsites')->where(["user.is_active" => 1, "user.contractor_id" => $model_master->contractor_id, "user_jobsite.jobsite_id" => $model_master->jobsite_id])->orderBy('employee_number')->all();
/*elseif (is_numeric($model_master->contractor_id)):
    $data_affected_user = app\models\User::find()->where(["user.is_active" => 1, "user.contractor_id" => $model_master->contractor_id])->orderBy('employee_number')->all();
elseif (is_numeric($model_master->jobsite_id)):
   /* $data_affected_user = app\models\User::find()->joinWith('userJobsites')->where(["user.is_active" => 1, "user_jobsite.jobsite_id" => $model_master->jobsite_id])->orderBy('employee_number')->all();*/
    /*$sqlQuery = "select * from dbo.[user] u join dbo.user_jobsite uj on uj.user_id = u.id where u.is_active = 1 and uj.jobsite_id = $model_master->jobsite_id order by employee_number desc";
       
    $data_affected_user_array = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
     $data_affected_user = json_decode(json_encode($data_affected_user_array));*/
endif;
if (isset($data_affected_user)):
    $data_affected_user_temp[""] = '-Select the affected employee ID number or name-';
    foreach ($data_affected_user as $obj) {
        $data_affected_user_temp[$obj->id] = $obj->employee_number . " - " . $obj->first_name . " " . $obj->last_name;
    }
    $data_affected_user = ( count($data_affected_user_temp) == 0 ) ? ["" => "-No users found-"] : $data_affected_user_temp;
else:
    $data_affected_user = ["" => "-"];
endif;

// Contractor
if (is_numeric($model_master->jobsite_id) && is_numeric($model_master->affected_user_id)&& is_numeric($model_master->contractor_id)):
	//modified to get all the contractors based on job site

    $sql = "SELECT distinct C.id, C.contractor FROM dbo.contractor C inner JOIN [dbo].[user] u ON C.id = U.contractor_id inner JOIN dbo.contractor_jobsite cj ON U.contractor_id = cj.contractor_id WHERE c.is_active = 1 AND cj.jobsite_id = $model_master->jobsite_id   AND U.is_active = 1";

    $contractorarray = Yii::$app->db->createCommand( $sql )->queryAll();
   
    $data_contractor = json_decode(json_encode($contractorarray));

    //app\models\Contractor::find()->joinWith('users')->joinWith('contractorJobsites')->where(["contractor.is_active" => 1, "user.contractor_id" => $model_master->contractor_id, "contractor_jobsite.jobsite_id" => $model_master->jobsite_id])->orderBy('contractor')->all();
elseif (is_numeric($model_master->affected_user_id)):
    $data_contractor = app\models\Contractor::find()->joinWith('users')->where(["contractor.is_active" => 1, "user.id" => $model_master->affected_user_id])->orderBy('contractor')->all();
elseif (is_numeric($model_master->jobsite_id)):
    $data_contractor = app\models\Contractor::find()->joinWith('contractorJobsites')->where(["contractor.is_active" => 1, "contractor_jobsite.jobsite_id" => $model_master->jobsite_id])->orderBy('contractor')->all();
endif;


if (isset($data_contractor)):
    $data_contractor_temp[""] = '-Select a contractor-';
    foreach ($data_contractor as $obj) {
        $data_contractor_temp[$obj->id] = $obj->contractor;
    }
    $data_contractor = ( count($data_contractor_temp) == 0 ) ? ["" => "-No contractors found-"] : $data_contractor_temp;
else:
    $data_contractor = ["" => "-"];
endif;

// Contractor
//    $data_contractor = ArrayHelper::map( app\models\Contractor::find()->asArray()->where( [ "is_active" => 1 ] )->orderBy('contractor')->all(), 'id', 'contractor' );
// Status
$data_status = ArrayHelper::map(app\models\AppCaseStatus::find()->where(["is_active" => 1])->asArray()->all(), 'id', 'status');
//$listData=['2'=>'CLOSED','1'=>'OPEN','3'=>'OVERDUE']; added for recognistion status should be closed always and disabled
// Priority
$data_priority = ArrayHelper::map(app\models\AppCasePriority::find()->where(["is_active" => 1])->asArray()->all(), 'id', 'priority');

// Trade
$data_trade = ArrayHelper::map(app\models\Trade::find()->where(["is_active" => 1])->orderBy('trade')->asArray()->all(), 'id', 'trade');

// Jobsite
//get all jobsite
//    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );
//get jobsite_id by user_affected_id
//    $data_user_jobsite = app\models\UserJobsite::find()->select( 'jobsite_id' )->where( [ 'user_id' => $model_master->affected_user_id ] )->asArray()->all();
//    //make array to query
//    $array_index_jobsite_id = ArrayHelper::getColumn( $data_user_jobsite, 'jobsite_id' );
// Sub jobsite
$data_sub_jobsite = ArrayHelper::map(app\models\SubJobsite::find()->where(["jobsite_id" => $model_master->jobsite_id, "is_active" => 1])->orderBy('subjobsite')->asArray()->all(), 'id', 'subjobsite');
if (count($data_sub_jobsite) == 0) {
    $data_sub_jobsite = ["" => "-"];
}

// Building
$data_building = ArrayHelper::map(app\models\Building::find()->where(["jobsite_id" => $model_master->jobsite_id, "is_active" => 1])->orderBy('building')->asArray()->all(), 'id', 'building');
if (count($data_building) == 0) {
    $data_building = ["" => "-"];
}

// Floor
$data_floor = ArrayHelper::map(app\models\Floor::find()->where(["building_id" => $model_master->building_id, "is_active" => 1])->orderBy('floor')->asArray()->all(), 'id', 'floor');
if (count($data_floor) == 0) {
    $data_floor = ["" => "-"];
}

// Area
$data_area = ArrayHelper::map(app\models\Area::find()->where(["floor_id" => $model_master->floor_id, "is_active" => 1])->orderBy('area')->asArray()->all(), 'id', 'area');
if (count($data_area) == 0) {
    $data_area = ["" => "-"];
}

// Foreman ID
if ($model_master->app_case_type_id != APP_CASE_INCIDENT) {
    $data_foreman = app\models\User::find()->where([
                "contractor_id" => $model_master->contractor_id,
                "role_id" => ROLE_CONTRACTOR_FOREMAN,
                "is_active" => 1
            ])->all();
    $data_foreman_temp[""] = '-';
    foreach ($data_foreman as $obj) {
        $data_foreman_temp[$obj->id] = $obj->employee_number . " - " . $obj->first_name . " " . $obj->last_name;
    }
    $data_foreman = ( count($data_foreman_temp) == 0 ) ? ["" => "-"] : $data_foreman_temp;
} else {
    $data_report_topic = ArrayHelper::map(app\models\ReportTopic::find()->where(["is_active" => 1])->orderBy('report_topic')->asArray()->all(), 'id', 'report_topic');
    $data_report_type = ArrayHelper::map(app\models\ReportType::find()->where(["is_active" => 1])->orderBy(['report_type' => SORT_DESC ])->asArray()->all(), 'id', 'report_type');
    $data_body_part = ArrayHelper::map(app\models\BodyPart::find()->where(["is_active" => 1])->orderBy('body_part')->asArray()->all(), 'id', 'body_part');
    $data_injury_type = ArrayHelper::map(app\models\InjuryType::find()->where(["is_active" => 1])->orderBy('injury_type')->asArray()->all(), 'id', 'injury_type');
    $data_causation_factor = ArrayHelper::map(app\models\CausationFactor::find()->where(["is_active" => 1])->orderBy('causation_factor')->asArray()->all(), 'id', 'causation_factor');
}

// Parent Safety Code
$data_parent_sfcode = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => null, "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');
$data_parent_sfcode_sec1 = [];
$data_parent_sfcode_sec2 = [];

if (count($data_parent_sfcode) == 0) {
    $data_parent_sfcode = ["" => "-"];
}

if (($model_master->isNewRecord) && (!$draftversion)) {
    $app_case_safety_code = '';
    $app_case_parent = "";
    $app_case_subsec1 = "";
    $app_case_subsec2 = "";
    $app_case_safety_code_getvalue = "";
     
} else {

    $app_case_safety_code_getvalue = $model_master->app_case_sf_code_id;

    $app_case_parent = "";
    $app_case_subsec1 = "";
    $app_case_subsec2 = "";

    if($draftversion)
   {
    if(isset($myJSON["osha-section"]) && $myJSON["osha-section"]!= ""){
        $app_case_parent = $myJSON["osha-section"];
    }
        if(isset($myJSON["osha-subsection1"]) && $myJSON["osha-subsection1"]!= ""){
        $app_case_subsec1 = $myJSON["osha-subsection1"];
    }
        if(isset($myJSON["osha-subsection2"]) && $myJSON["osha-subsection2"]!= ""){
        $app_case_subsec2 = $myJSON["osha-subsection2"];
    }
   }

   $yes = array();
   //$app_case_safety_code=array();
    $app_case_safety_code = app\models\AppCaseSfCode::find()->select('id, code, description,parent_id')->where(['id' => $model_master->app_case_sf_code_id, "is_active" => 1])->one();
    
      if((is_array($yes)) && (isset($app_case_safety_code)))
      {

     if($app_case_safety_code['parent_id'] == null){

        //Loading the Array Sub Section array if data exist
        $data_parent_sfcode_sec1 = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => $app_case_safety_code['id'], "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');
           
        $app_case_parent = $app_case_safety_code['id'];
     }else{


        $app_case_safety_code_sub1 = app\models\AppCaseSfCode::find()->select('id, code, description,parent_id')->where(['id' => $app_case_safety_code['parent_id'], "is_active" => 1])->one();

        //Loading the Array Sub Section array if data exist
        $data_parent_sfcode_sec1 = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => $app_case_safety_code['parent_id'], "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');

          if($app_case_safety_code_sub1['parent_id'] == null)
          {

            $data_parent_sfcode_sec2 = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => $app_case_safety_code['id'], "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');

            $app_case_subsec1 = $app_case_safety_code['id'];
           $app_case_parent = $app_case_safety_code_sub1['id'];

          }else
          {
             $app_case_safety_code_sub2 = app\models\AppCaseSfCode::find()->select('id, code, description,parent_id')->where(['id' => $app_case_safety_code_sub1['parent_id'], "is_active" => 1])->one();

             $data_parent_sfcode_sec1 = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => $app_case_safety_code_sub2['id'], "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');

              $data_parent_sfcode_sec2 = ArrayHelper::map(app\models\AppCaseSfCode::find()->select(['id','parent_id', new \yii\db\Expression("CONCAT(code, '-', description) as description")])->where(["parent_id" => $app_case_safety_code['parent_id'], "is_active" => 1])->orderBy('code')->asArray()->all(), 'id', 'description');

              $app_case_subsec2 = $app_case_safety_code['id'];
             $app_case_subsec1 = $app_case_safety_code_sub1['id'];
             $app_case_parent = $app_case_safety_code_sub2['id'];

          }
     }
   }
}
$affected_employee_number = '';
$selected_contractor = 0;
$selected_jobsite = 0;
$type_id = $model_master->app_case_type_id;
if ($model_master->isNewRecord) {
    $isedit = $model_master->isNewRecord;
    $selected_contractor = 0;
    $selected_jobsite = 0;
    if($draftversion && isset($myJSON["AppCase"]["contractor_id"]) && ($myJSON["AppCase"]["contractor_id"] != ""))
   {

    $isedit = 0;
    $selected_contractor = $myJSON["AppCase"]["contractor_id"];
    $selected_jobsite = $myJSON["AppCase"]["jobsite_id"];
    if(isset($myJSON["AppCase"]["affected_user_id"]) && $myJSON["AppCase"]["affected_user_id"] != ""){
           $selectedaffectedUser = $myJSON["AppCase"]["affected_user_id"];

    $affected_employee_number = app\models\User::find()->select('first_name,last_name,employee_number')->where(['id' => $selectedaffectedUser, "is_active" => 1])->asArray()->one();
    if(isset($affected_employee_number)){
        $affected_employee_number = $affected_employee_number['employee_number'] . ' - ' . $affected_employee_number['first_name'] . ' ' . $affected_employee_number['last_name']; 
    }
    }     
   } 
} else {
    $isedit = 0;
    $selected_contractor = $model_master->contractor_id;
    $selected_jobsite = $model_master->jobsite_id;
    $selectedaffectedUser = $model_master->affected_user_id;
    $affected_employee_number = app\models\User::find()->select('first_name,last_name,employee_number')->where(['id' => $model_master->affected_user_id, "is_active" => 1])->asArray()->one();
    if(isset($affected_employee_number)){
    $affected_employee_number = $affected_employee_number['employee_number'] . ' - ' . $affected_employee_number['first_name'] . ' ' . $affected_employee_number['last_name'];
    }
    $attachment_allowed = app\models\AppCase::find()->select('is_attachment')->where(['id' => $model_master->id])->one();
}

//User has roles Jobsite Administrator or System Administrator?
$listData=['1'=>'YES','0'=>'NO'];
?>

<!-- Preload -->
<style type="text/css">
   .loader {
      border: 10px solid #f3f3f3; /* Light grey */
      border-top: 10px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 2s linear infinite;
      text-align: center;
      margin: auto;
      padding-bottom: 15px;
    }

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
.circleloader {
    background: url(./img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}

/*CSS for Drop Down*/
.select2-container {
    width: 100% !important;
    padding-top: 1% !important;
}

.select2-container--default .select2-selection--single {
    background-color: #fff !important;
    border: 0px solid #aaa !important;
    border-bottom: 1px solid #e0e0e0 !important;
    border-radius: 0px !important;
}

.osha-errorclass{
    margin-bottom : 7px !important;
}

.jsBeforeSubmitFormBtnBrother {
    cursor: progress;
}
.jsBeforeSubmitFormBtnBrother .glyphicon{
    -webkit-animation: glyphicon-spin 2s infinite linear;
    animation: glyphicon-spin 2s infinite linear;
}

</style>

<!-- Safety Code Modal Selection -->
<div class="modal fade in" id="safety-code-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Safety Code</h4>
            </div>
            <div id="safety-code-tree-view-container" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Safety Code Modal Selection -->

<!-- show affected employee model -->
<div class="modal" id="affected-employee-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Affected Employee Search</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12"><p>Select Affected Employee</p></div>
                    <div id="affected-employee-creator-dropdown-container" class="col-sm-12"></div>
                </div>
            </div>
            <div id="btn-affected-employee-creator-container" class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="offender-user" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="p-b-0 p-l-0 modal-title">Repeat Offender</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12" id="offenderuserandissues"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="discardconfirm-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <p>Are you sure you want to discard the issue?</p>
      </div>
      <div class="modal-footer">
        <a class="btn btn-default" id="discardyes-bth">Yes</a>
        <a class="btn btn-primary" id="discardno-bth">No</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="jobsite-form">
    <?= Html::img('@web/img/IssueType-' . $model_master->app_case_type_id . '.png', ['style' => 'position:absolute; width:60px; right:26px; top:23px;']) ?>

    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'id' => 'IssueForm',
                    'style' => 'margin-top: 55px',
        'enctype' => "multipart/form-data",
        'onsubmit' => "return validateForm()",
                ],
    ]);
    ?>

    <div class="row">
        <!-- Jobsite -->
        <div class="col-sm-6">
            <div class="form-group fg-line">

                <?=
                $form->field($model_master, 'jobsite_id')->dropDownList($data_jobsite, [
                    'prompt' => '-Select a jobsite-',
                    'class' => 'form-control select2-dropdown',
                    'onchange' => 'jobsiteIdChange(this.value)'
                ])
                ?>
            </div>
        </div>
        <!-- Jobsite -->
        <!-- Sub jobsite -->
        <div class="col-sm-6">
            <?=
            $form->field($model_master, 'sub_jobsite_id')->dropDownList($data_sub_jobsite, [
                'id' => 'sub-jobsite-id-select',
                'prompt' => '-Select a subjobsite-',
            ])
            ?>
        </div>
        <!-- Sub jobsite -->
    </div>

    <?php if ($model_master->app_case_type_id == APP_CASE_INCIDENT): ?>

        <div class="row">
            <!-- Building -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'building_id')->dropDownList($data_building, [
                    'prompt' => '-Select a building-',
                    'onchange' => 'buildingIdChange(this.value)',
                ])
                ?>
            </div>
            <!-- Building -->
            <!-- Floor -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'floor_id')->dropDownList($data_floor, [
                    'prompt' => '-Select the Floor-',
                    'onchange' => 'floorIdChange(this.value)',
                ])
                ?>
            </div>
            <!-- Floor -->
        </div>

        <div class="row">
            <!-- Area -->
            <div class="col-sm-6">
                <div class="form-group fg-line">
                    <?=
                    $form->field($model_master, 'area_id')->dropDownList($data_area, [
                       'prompt' => '-Select the Area-',
                        'id' => 'area-id-select',
                        'class' => 'form-control'
                    ])
                    ?>
                </div>
            </div>
            <!-- Area -->
            <!-- Trade -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'trade_id')->dropDownList($data_trade, [
                    'prompt' => '-Select the trade of the employee-',
                ])
                ?>
            </div>
            <!-- Trade -->
        </div>

        <div class="row">
            <!-- Contractor -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'contractor_id')->dropDownList($data_contractor, [
                    'onchange' => 'contractorIdChange(this.value, $("#appcase-jobsite_id").val() )',
                    'class' => 'form-control select2-dropdown'
                ])
                ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model_master, 'affected_user_id')->textInput() ?>
                <div class="form-group">
                    <label for="input-affected_employee-display" class="control-label">Affected Employee</label>
                    <input type="hidden" id ="hdnAffectedUsr" name ="hdnAffectedUsr" value="<?= $model_master->affected_user_id ?>">                 
                    <input type="text" maxlength="255" value="<?= $affected_employee_number ?>" name=""
                           class="form-control truncate-no-abs input-readonly" id="input-affected_employee-display"
                           onfocus="showModalToAffectedEmployee(this.value)"
                           data-trigger="hover" data-toggle="popover" data-placement="top" 
                           data-content="" title="" data-original-title=""  readonly>
                    <div class="help-block affected-emp-error"></div>

                    <div id="divoffenderuser" style="display: none">
                    	 <input type="hidden" id ="reptoff" name ="reptoff" value="false">
                        <h5 style="font-size: 13px;color: #f44336;" >Repeat Offender! <a href ="#" style="color: #f44336" onclick ="showoffernderUser()"><u> Click here</u> </a> to view the previous issues of Affected Employee</h5>
                    </div> 
                </div> 
            </div>  
        </div>  

        <div class="row">
            <!-- Status -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'app_case_status_id')->dropDownList($data_status, [
                    'prompt' => '-Select the status-',
                ])
                ?>
            </div>
            <!-- Status -->
            <!-- Priority -->
            <div class="col-sm-6">
                <?=
                $form->field($model_master, 'app_case_priority_id')->dropDownList($data_priority, [
                    'prompt' => '-Select a priority level-',
                ])
                ?>
            </div>
            <!-- Priority -->
        </div>
        <div class="row">
            <!-- OSHA / Safety Code -->
            <div class="col-sm-6 hidden">

                <div class="form-group">
                    <label for="input-safety-code-display" class="control-label">Safety Code</label>
                    <input type="hidden" value="<?= $model_master->app_case_sf_code_id ?>">
                    <input type="text" maxlength="255" name=""
                           class="form-control truncate-no-abs input-readonly" id="input-safety-code-display"
                           onfocus="safetyCodeInputClick()" data-trigger="hover" data-toggle="popover" data-placement="top"
                           data-content="" title="" data-original-title="" readonly>

                    <div class="help-block"></div>
                </div>
            </div> 

    <div class="form-group field-appcase-app_case_sf_code_id required fg-line">
        <label class="control-label" for="appcase-app_case_sf_code_id">Safety Code</label>
        <input type="hidden" id="appcase-app_case_sf_code_id" class="form-control" name="AppCase[app_case_sf_code_id]" value="<?=$model_master->app_case_sf_code_id?>"  aria-required="true">

        <div class="help-block"></div>
        </div>
        <div class="col-sm-6">

            <div class="form-group field-osha-section required fg-line">
            <label class="control-label" for="osha-section">Safety Code</label>

            <?= Html::dropDownList('osha-section', $app_case_parent, $data_parent_sfcode, ['prompt' => '-Select the applicable OSHA or WT Safety Code-','class' => 'form-control select2-dropdown', 'id'=> 'osha-section', 'onchange' => 'Get_Sf_sec(this.value, 0)']) ?>


            <div class="help-block osha-section_error"></div>
            </div>
        </div>

    <div class="col-sm-6">
            <div class="form-group field-osha-subsection1 required fg-line">
            <label class="control-label" for="osha-subsection1">Section</label>
             <?= Html::dropDownList('osha-subsection1', $app_case_subsec1, $data_parent_sfcode_sec1, ['prompt' => '-Select applicable safety code section-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection1','onchange' => 'Get_Sf_sec(this.value, 1)']) ?>

            <div class="help-block osha-subsection1_error"></div>
            </div>
        </div>
       </div>
        <div class="row">

        <div class="col-sm-6">
            <div class="form-group field-osha-subsection2 required fg-line">
            <label class="control-label" for="osha-subsection2">Subsection</label>
            <?= Html::dropDownList('osha-subsection2', $app_case_subsec2, $data_parent_sfcode_sec2, ['prompt' => '-Select the applicable safety code subsection-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection2','onchange' => 'Get_Sf_sec(this.value, 2)']) ?>

            <div class="help-block osha-subsection2_error"></div>
            </div>
        </div>
            <!-- OSHA / Safety Code -->
            <!-- Report Type -->

            <!-- Report Topic -->
            <div class="col-sm-6">
                <?= $form->field($model_detail, 'report_topic_id')->dropDownList($data_report_topic, ['prompt' => '-Select the proper report topic-']) ?>
            </div>
            <!-- Report Topic -->
        </div>

        <div class="row">
            <!-- Report type -->
            <div class="col-sm-6">
                <?=
                $form->field($model_detail, 'report_type_id')->dropDownList($data_report_type, ['prompt' => '-Select the proper report type-',
                    'id' => 'report_type_id'])
                ?>
            </div>
            <!-- Report type -->

            <!-- causation factor -->
            <div class="col-sm-6">
                <?= $form->field($model_detail, 'causation_factor')->dropDownList($data_causation_factor, ['prompt' => '-Select the proper causation factor-', 'id' => 'causation_id'])
                ?>
            </div>
            <!-- causation factor -->
        </div>

        <div class="row">
            <!-- Body Part -->
            <div class="col-sm-6">
                <?= $form->field($model_detail, 'body_part_id')->dropDownList($data_body_part, ['prompt' => '-Select  the affected body part, illness or N/A if not applicable-']) ?>
            </div>
            <!-- Body Part -->

            <!-- Injury Type -->
            <div class="col-sm-6">
                <?= $form->field($model_detail, 'injury_type_id')->dropDownList($data_injury_type, ['prompt' => '-Select  the proper injury type resulting from this incident or N/A if not applicable-']) ?>
            </div>
            <!-- Injury Type -->

        </div>
        <div class="row">
            <!-- Incident time -->
            <div class="col-sm-6">
                <div class="form-group">
                    <?=
                    $form->field($model_detail, 'incident_time')->textInput([
                        'class' => 'form-control time-picker input-readonly'
                    ])
                    ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <!-- Incident time -->

            <!-- Incident date -->
            <div class="col-sm-6">
                <div class="form-group">
                    <?=
                    $form->field($model_detail, 'incident_date')->textInput([
                        'class' => 'form-control incident-date-picker input-readonly'
                    ])
                    ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <!-- Incident date -->
        </div>

        <div class="row">
        <!-- Is Dart  -->
        <div class="col-sm-6">
                <?=
                $form->field($model_detail, 'is_dart')->dropDownList($listData, ['prompt' => 'Has this incident resulted in Days Away Restricted and Transfer ?','onchange' => 'onLostTimeClick(this.value)']);
                ?>
        </div>
        <!-- Is Dart  -->
        <!-- Dart Time -->
        <div class="col-sm-6">
            <?= $form->field($model_detail, 'dart_time')->textInput(array('placeholder' => 'Enter number of DART days')) ?>
        </div>
        <!-- Dart Time -->      
        </div>

        <div class="row">
            <!--is lost time-->
            <div class="col-sm-6">
                <?=
                $form->field($model_detail, 'is_lost_time')->dropDownList($listData, ['prompt' => '-Has this incident resulted in a lost time?-','onchange' => 'onLostTimeClick(this.value)'])
                ?>
            </div>
            <!--is lost time-->

            <!-- Recordable -->
            <div class="col-sm-6">
                <?=
                $form->field($model_detail, 'recordable')->dropDownList($listData, ['prompt' => '-Is this incident clasified as recordable incident?-'])
                ?>
            </div>

        </div>

     <div class="row">
        
        <!-- Lost Time -->
        <div class="col-sm-6">
            <?= $form->field($model_detail, 'lost_time')->textInput(array('placeholder' => 'Enter number of lost time days')) ?>
        </div>
        <!-- Lost Time -->
        
          <!-- Property Damage -->
            <div class="col-sm-6">
                <?=
                $form->field($model_detail, 'is_property_damage')->dropDownList($listData, ['prompt' => '-Has this incident resulted in property damage?-'])
                ?>
            </div>
           <!-- Property Damage -->
    </div>

    <!-- Send notification -->
    <?php if ($canSkipNotifications) { ?>
            <div class="col-sm-6">
                <div class="form-group field-appcaseincident-area_id required fg-line">
                    <!--label class="control-label m-r-20">Notifications</label-->
                    <label class="checkbox checkbox-inline m-r-20">
                        <input type="checkbox" value="1" name="skip"><i
                            class="input-helper"></i> Skip notifications
                    </label>
                    <div class="help-block"></div>
                </div>
            </div>
        <?php } ?>
        <!-- Send notification -->
    
        

        <?php if ($model_master->app_case_type_id != APP_CASE_OBSERVATION): ?>
    <?php if ($model_master->app_case_type_id == APP_CASE_INCIDENT): ?>
<div class="row">
    <!-- Additional Information -->
    <input type="hidden" id="isincident" value="true">
    <div class="col-sm-12">
        <?= $form->field($model_master, 'additional_information')->textarea(['rows' => 6]) ?>
    </div>
    <!-- Additional Information -->
</div>
<?php else: ?>
        <div class="row">
    <!-- Additional Information -->
    <div class="col-sm-12">
        <?= $form->field($model_master, 'additional_information')->textarea(['rows' => 6])->label('Description') ?>
    </div>
        <!-- Additional Information -->
    </div>
<?php endif; ?>
<?php endif; ?>
    <?php if ($model_master->isNewRecord) : ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group field-attachment required fg-line">
            <label class="checkbox checkbox-inline m-r-20">
                <input type="checkbox" value="1" class="attachment-checkbox"  onchange="onChange(this)"
                       name="AppCase[photo_allowed]"   id="chkattachment"  
                       <?php if($draftversion):?>
                        
                         <?php echo (($draftversion) && $draftattachment == 1) ? "" : "disabled='disabled'" ?>
                        <?php else : ?>
                            <?php echo ($model_master !== null && $model_master->photo_allowed == 1 ) ? "checked" : " disabled='disabled'"?>
                          <?php endif ?>                               
                        ><i
                       class="input-helper"></i>Attachment
            </label>
            <div class="filepond-error"></div>
            <div id="dvfileattachment" style="display: none" >
                <input type="file" class="upload-file filepond" name="attachment[]" multiple > 
            </div>
        </div> 
    </div> 
</div> 
<?php endif; ?> 

<?php if (!($model_master->isNewRecord)) : ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-attachment required fg-line">
                <label class="checkbox checkbox-inline m-r-20">
                    <input type="checkbox" value="1" class="attachment-checkbox"  onchange="onChangeInEdit(this)"
                           name="AppCase[photo_allowed]" id="chkattachmentedit" 
                           <?php echo ($model_master->is_attachment == 1 ) ? "checked" : " " ?> ><i
                           class="input-helper"></i>Attachment
                </label>

                <?php if ($model_master->is_attachment == 1) : ?>
                    <div class="filepond-error"></div>
                    <div id="dvfileattachmentinedit" style="display: block">
                        <input type="file" class="upload-file filepond" name="attachment[]"  multiple> 
                    </div>
                <?php else : ?>
                    <div class="filepond-error"></div>
                    <div id="dvfileattachmentinedit" style="display: none">
                        <input type="file" class="upload-file filepond" name="attachment[]" multiple> 
                    </div>
                <?php endif; ?> 
                <div class="help-block"></div>
            </div> 
        </div> 
    </div> 

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-attachment required fg-line">
            <?php for($count = 0;(!empty($model_master->attachments[$count]["destination_url"]) && count($model_master->attachments)>=$count);$count++){ ?>
                <?php if ($model_master->attachments[$count]["type"] == 'blob') : ?>
                            <ul>
                            <?php $destinationURL = $model_master->attachments[$count]["destination_url"]; $mimeType = $model_master->attachments[$count]["mimeType"];  $mimeType_blob_url = $mimeType.",".$destinationURL;   ?>
                                
                                    <li> <a href=<?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION);
                                                    if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'JPG') {
                                                        echo $destinationURL;
                                                    } else {
                                                        echo "javascript:void(0)";
                                                    }  ?> <?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION);
                                                    if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'JPG') {
                                                        echo "target='_blank'";
                                                    } else {
                                                        echo "onclick = saveFile('" . $mimeType_blob_url . "')";
                                                    }  ?>> <?php $filename = explode("/", $destinationURL);echo $filename[4]; ?> </a> </li>
                                
                            </ul>
                            <?php else: ?>
                            <ul>
                            <?php $filename = $model_master->attachments[$count]["destination_url"]   ?>
                                    <li> <a href="<?php echo "/files/" . $filename; ?>" <?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION);
                                                                                            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'JPG') {
                                                                                                echo "target='_blank'";
                                                                                            } else {
                                                                                                echo 'download';
                                                                                            } ?>> <?php echo $filename; ?> </a> </li>    
                            </ul>
                        <?php endif; ?>
                        <?php }?>  
            </div>
        </div>
    </div>

<?php endif; ?>

    
   

<?php else: ?>

    <div class="row">
        <!-- Contractor -->
        <div class="col-sm-6">
            <?=
            $form->field($model_master, 'contractor_id')->dropDownList($data_contractor, [
                'onchange' => 'contractorIdChange(this.value, $("#appcase-jobsite_id").val() )',
                'class' => 'form-control select2-dropdown'
            ])
            ?>
        </div>

        <!-- Affected user -->
        <div class="col-sm-6">
            <?= $form->field($model_master, 'affected_user_id')->textInput() ?>
            <div class="form-group fg-line">
                <input type="hidden" id ="hdnAffectedUsr" name ="hdnAffectedUsr"value="<?= $model_master->affected_user_id ?>">
                <label for="input-affected_employee-display" class="control-label">Affected Employee</label>
                <input type="text" maxlength="255"value="<?= $affected_employee_number ?>" name=""
                       class="form-control truncate-no-abs input-readonly" id="input-affected_employee-display"
                       onfocus="showModalToAffectedEmployee(this.value)"
                       data-trigger="hover" data-toggle="popover" data-placement="top" 
                       data-content="" title="" data-original-title=""  readonly>
                <div class="help-block affected-emp-error"></div>
                <?php if ($model_master->app_case_type_id == APP_CASE_VIOLATION) : ?>
                    <div id="divoffenderuser" style="display: none">              
                        <input type="hidden" id ="reptoff" name ="reptoff" value="false">            
                        <h5 style="font-size: 13px;color: #f44336;" >Repeat Offender! <a href ="#" style="color: #f44336" onclick ="showoffernderUser()"><u> Click here</u> </a> to view the previous issues of Affected Employee</h5>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <!-- Affected user -->
    </div>
    <div class="row">
        <!-- Building -->
        <div class="col-sm-6">
            <?=
            $form->field($model_master, 'building_id')->dropDownList($data_building, [
                'prompt' => '-Select a building-',
                'onchange' => 'buildingIdChange(this.value)',
                'class' => 'form-control'
            ])
            ?>
        </div>
        <!-- Building -->
        <!-- Floor -->
        <div class="col-sm-6">
            <?=
            $form->field($model_master, 'floor_id')->dropDownList($data_floor, [
                'prompt' => '-Select the Floor-',
                'onchange' => 'floorIdChange(this.value)',
                'class' => 'form-control'
            ])
            ?>
        </div>
        <!-- Floor -->
    </div>

  <?php if ($model_master->app_case_type_id == APP_CASE_RECOGNITION): ?>

<div class="row">
        <!-- Area -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <?=
                $form->field($model_master, 'area_id')->dropDownList($data_area, [
                    'prompt' => '-Select the Area-',
                    'id' => 'area-id-select',
                    'class' => 'form-control'
                ])
                ?>
            </div>
        </div>
        
         <div class="col-sm-6">
                <?=
                $form->field($model_master, 'app_case_priority_id')->dropDownList($data_priority, [
                'prompt' => '-Select a priority level-',
                ])
                ?>
        </div>
</div>

    <div class="row">
        <!-- OSHA / Safety Code -->
        <div class="col-sm-6 hidden">
           
            <div class="form-group">
                <label for="input-safety-code-display" class="control-label">Safety Code</label>
                <input type="hidden" value="<?= $model_master->app_case_sf_code_id ?>">
                <input type="text" maxlength="255" name=""
                       class="form-control truncate-no-abs input-readonly" id="input-safety-code-display"
                       onfocus="safetyCodeInputClick()" data-trigger="hover" data-toggle="popover" data-placement="top"
                       data-content="" title="" data-original-title="" readonly>

                <div class="help-block"></div>
            </div>
        </div>

            <div class="form-group field-appcase-app_case_sf_code_id required fg-line">
        <label class="control-label" for="appcase-app_case_sf_code_id">Safety Code</label>
       <input type="hidden" id="appcase-app_case_sf_code_id" class="form-control" name="AppCase[app_case_sf_code_id]" value="<?=$model_master->app_case_sf_code_id?>"  aria-required="true">

        <div class="help-block"></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group field-osha-section required fg-line">
            <label class="control-label" for="osha-section">Safety Code</label>
             <?= Html::dropDownList('osha-section', $app_case_parent, $data_parent_sfcode, ['prompt' => '-Select the applicable OSHA or WT Safety Code-','class' => 'form-control select2-dropdown', 'id'=> 'osha-section', 'onchange' => 'Get_Sf_sec(this.value, 0)']) ?>
          

            <div class="help-block osha-section_error"></div>
            </div>
        </div>

    <div class="col-sm-6">
            <div class="form-group field-osha-subsection1 required fg-line">
            <label class="control-label" for="osha-subsection1">Section</label>
             <?= Html::dropDownList('osha-subsection1', $app_case_subsec1, $data_parent_sfcode_sec1, ['prompt' => '-Select applicable safety code section-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection1','onchange' => 'Get_Sf_sec(this.value, 1)']) ?>

            <div class="help-block osha-subsection1_error"></div>
            </div>
        </div>
</div>
        <div class="row">
                <div class="col-sm-6">
            <div class="form-group field-osha-subsection2 required fg-line">
            <label class="control-label" for="osha-subsection2">Subsection</label>
            <?= Html::dropDownList('osha-subsection2', $app_case_subsec2, $data_parent_sfcode_sec2, ['prompt' => '-Select the applicable safety code subsection-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection2','onchange' => 'Get_Sf_sec(this.value, 2)']) ?>

            <div class="help-block osha-subsection2_error"></div>
            </div>
        </div>
          
        <!-- OSHA / Safety Code -->
        
         <!-- Trade -->
        <div class="col-sm-6">
                <?=
                $form->field($model_master, 'trade_id')->dropDownList($data_trade, [
                'prompt' => '-Select the trade of the employee-',
                ])
                ?>
        </div>
        <!-- Trade -->
    </div>
    
       <div class="row">
        <!-- Foreman ID -->
        <div class="col-sm-6">
            <?= $form->field( $model_detail, 'foreman_id' )->dropDownList( $data_foreman, [ 'id' => 'foreman-id-select' ] ) ?>
        </div>
        <!-- Foreman ID -->
        
          <!-- Correction Date -->
                 
        <div class="col-sm-6">
              <?php if ($model_master->app_case_type_id != APP_CASE_RECOGNITION): ?>
                    <?=
                    $form->field($model_detail, 'correction_date')->textInput([
                "class"       => "form-control date-picker",
                "data-toggle" => "dropdown",
                'id' => 'correction-date-id'
            ] )
                ?>
                  <?php endif ?>
        </div>
         </div>
 <?php else:  ?>
    <div class="row">
        <!-- Area -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <?=
                $form->field($model_master, 'area_id')->dropDownList($data_area, [
                    'prompt' => '-Select the Area-',
                    'id' => 'area-id-select',
                    'class' => 'form-control'
                ])
                ?>
            </div>
        </div>
        <!-- Area -->
        
         <?php if ($model_master->app_case_type_id == APP_CASE_RECOGNITION): ?>
       <!--  <div class="col-sm-6">
           //</?=
           // $form->field($model_master, 'app_case_status_id')->dropDownList($listData,['disabled' => true, 'id' > 'status-id'])
            /?> */
        </div> -->
        <?php elseif($model_master->app_case_type_id == APP_CASE_VIOLATION): ?>
         <div class="col-sm-6">
            <?=
            $form->field($model_master, 'app_case_status_id')->dropDownList($data_status, [
                'prompt' => '-Select the status-'
            ])
            ?>
        </div>

        <?php else: ?>
         <!-- Status -->
      <div class="col-sm-6">
            <?=
            $form->field($model_master, 'app_case_status_id')->dropDownList($data_status, [
                'prompt' => '-Select the status-'
            ])
            ?>
        </div>
        <!-- Status -->
        <?php endif; ?>
      
    </div>
    <div class="row">
        <!-- OSHA / Safety Code -->
        <div class="col-sm-6 hidden">
<!--             <?= $form->field($model_master, 'app_case_sf_code_id')->textInput() ?> -->
            <div class="form-group">
                <label for="input-safety-code-display" class="control-label">Safety Code</label>
                <input type="hidden" value="<?= $model_master->app_case_sf_code_id ?>">
                <input type="text" maxlength="255" value="" name=""
                       class="form-control truncate-no-abs input-readonly" id="input-safety-code-display"
                       onfocus="safetyCodeInputClick()" data-trigger="hover" data-toggle="popover" data-placement="top"
                       data-content="" title="" data-original-title="" readonly>

                <div class="help-block"></div>
            </div>
        </div>

            <div class="form-group field-appcase-app_case_sf_code_id required fg-line">
        <label class="control-label" for="appcase-app_case_sf_code_id">Safety Code</label>
        <input type="hidden" id="appcase-app_case_sf_code_id" class="form-control" name="AppCase[app_case_sf_code_id]"  aria-required="true" value="<?=$model_master->app_case_sf_code_id?>">

        <div class="help-block"></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group field-osha-section required fg-line">
            <label class="control-label" for="osha-section">Safety Code</label>
             <?= Html::dropDownList('osha-section', $app_case_parent, $data_parent_sfcode, ['prompt' => '-Select the applicable OSHA or WT Safety Code-','class' => 'form-control select2-dropdown', 'id'=> 'osha-section', 'onchange' => 'Get_Sf_sec(this.value, 0)']) ?>

            <div class="help-block osha-section_error"></div>
            </div>
        </div>

    <div class="col-sm-6">
            <div class="form-group field-osha-subsection1 required fg-line">
            <label class="control-label" for="osha-subsection1">Section</label>
            <?= Html::dropDownList('osha-subsection1', $app_case_subsec1, $data_parent_sfcode_sec1, ['prompt' => '-Select applicable safety code section-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection1','onchange' => 'Get_Sf_sec(this.value, 1)']) ?>

            <div class="help-block osha-subsection1_error"></div>
            </div>
        </div>
</div>
        <div class="row">
                <div class="col-sm-6">
            <div class="form-group field-osha-subsection2 required fg-line">
            <label class="control-label" for="osha-subsection2">Subsection</label>
            <?= Html::dropDownList('osha-subsection2', $app_case_subsec2, $data_parent_sfcode_sec2, ['prompt' => '-Select the applicable safety code subsection-','class' => 'form-control select2-dropdown', 'id'=> 'osha-subsection2','onchange' => 'Get_Sf_sec(this.value, 2)']) ?>
            <div class="help-block osha-subsection2_error"></div>
            </div>
        </div>
           
        <!-- OSHA / Safety Code -->
        
     <!-- Priority -->
        <div class="col-sm-6">
        <?=
        $form->field($model_master, 'app_case_priority_id')->dropDownList($data_priority, [
                'prompt' => '-Select a priority level-',
        ])
        ?>
        </div>
        <!-- Priority -->
    </div>
    <div class="row">
        <!-- Foreman ID -->
        <div class="col-sm-6">
            <?= $form->field( $model_detail, 'foreman_id' )->dropDownList( $data_foreman, [ 'id' => 'foreman-id-select' ] ) ?>
        </div>
        <!-- Foreman ID -->
        <!-- Trade -->
        <div class="col-sm-6">
        <?=
        $form->field($model_master, 'trade_id')->dropDownList($data_trade, [
                'prompt' => '-Select the trade of the employee-',
        ])
        ?>
        </div>
        <!-- Trade -->
    </div>
    <?php if ($model_master->app_case_type_id == APP_CASE_VIOLATION): ?>
    <div class="row">
        <!-- Correction Date -->
        <div class="col-sm-6">
        <?=
        $form->field($model_detail, 'correction_date')->textInput([
                "class"       => "form-control date-picker",
                "data-toggle" => "dropdown",
               // "onblur" => "onDateChange(this)" , 
                'id' => 'correction-date-id'
            ] )
                ?>
             <div>
                    <h5 style="font-size: 13px;color: #f44336;margin-top: 10px; margin-bottom: 10px;">For past date, the request would be saved as "Closed"</h5>
        </div>
        </div>
                    
    <?php endif; ?>
        <!-- Correction Date --> 
    <?php if ($model_master->app_case_type_id == APP_CASE_OBSERVATION): ?>
        <div class="row">
            <!-- Observation Date -->
            <div class="col-sm-6">
                <?= $form->field($model_detail, 'correction_date')->textInput([
                        'class' => 'form-control incident-date-picker input-readonly'
                    ]) ?>
                    
            </div>
            <!-- Observation Date -->
          <?php endif; ?> 
        </div>
 <?php endif; ?>
        
        <?php if($canSkipNotifications){?>
                    <div class="col-sm-6">
                        <div class="form-group field-appcaseincident-area_id required fg-line">
                            <!--label class="control-label m-r-20">Notifications</label-->
                            <label class="checkbox checkbox-inline m-r-20">
                                <input type="checkbox" value="1" name="skip"><i
                                    class="input-helper"></i> Skip notifications
                            </label>

                            <div class="help-block"></div>
                        </div>
                    </div>
        <?php }?>
        <?php if ($model_master->app_case_type_id == APP_CASE_OBSERVATION): ?>
        
        <div class="row">
    <!-- Additional Information -->
    <div class="col-sm-12">
        <?= $form->field($model_master, 'additional_information')->textarea(['rows' => 6])->label('Description') ?>
    </div>
        <!-- Additional Information -->
    </div>
        <div class="row">
            <!-- Coaching Provider -->
            <div class="col-sm-12">
                <?= $form->field($model_detail, 'coaching_provider')->textarea(['rows' => 6]) ?>
            </div>
            <!-- Coaching Provider -->
        </div>
    <?php endif; ?>
    <?php if ($model_master->app_case_type_id != APP_CASE_OBSERVATION): ?>
    <?php if ($model_master->app_case_type_id == APP_CASE_INCIDENT): ?>
<div class="row">
    <!-- Additional Information -->
    <input type="hidden" id="isincident" value="true">
    <div class="col-sm-12">
        <?= $form->field($model_master, 'additional_information')->textarea(['rows' => 6]) ?>
    </div>
    <!-- Additional Information -->
</div>
<?php else: ?>
        <div class="row">
    <!-- Additional Information -->
    <div class="col-sm-12">
        <?= $form->field($model_master, 'additional_information')->textarea(['rows' => 6])->label('Description') ?>
    </div>
        <!-- Additional Information -->
    </div>
<?php endif; ?>
<?php endif; ?>
    <?php if ($model_master->isNewRecord) : ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group field-attachment required fg-line">
            <label class="checkbox checkbox-inline m-r-20">
                <input type="checkbox" value="1" class="attachment-checkbox"  onchange="onChange(this)"
                       name="AppCase[photo_allowed]"   id="chkattachment"  
                       <?php if($draftversion):?>
                        
                         <?php echo (($draftversion) && $draftattachment == 1) ? "" : "disabled='disabled'" ?>
                        <?php else : ?>
                            <?php echo ($model_master !== null && $model_master->photo_allowed == 1 ) ? "checked" : " disabled='disabled'"?>
                          <?php endif ?>                               
                        ><i
                       class="input-helper"></i>Attachment
            </label>
            <div class="filepond-error"></div>
            <div id="dvfileattachment" style="display: none" >
                <input type="file" class="upload-file filepond" name="attachment[]" multiple > 
            </div>
        </div> 
    </div> 
</div> 
<?php endif; ?> 

<?php if (!($model_master->isNewRecord)) : ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-attachment required fg-line">
                <label class="checkbox checkbox-inline m-r-20">
                    <input type="checkbox" value="1" class="attachment-checkbox"  onchange="onChangeInEdit(this)"
                           name="AppCase[photo_allowed]" id="chkattachmentedit" 
                           <?php echo ($model_master->is_attachment == 1 ) ? "checked" : " " ?> ><i
                           class="input-helper"></i>Attachment
                </label>

                <?php if ($model_master->is_attachment == 1) : ?>
                    <div class="filepond-error"></div>
                    <div id="dvfileattachmentinedit" style="display: block">
                        <input type="file" class="upload-file filepond" name="attachment[]"  multiple> 
                    </div>
                <?php else : ?>
                    <div class="filepond-error"></div>
                    <div id="dvfileattachmentinedit" style="display: none">
                        <input type="file" class="upload-file filepond" name="attachment[]" multiple> 
                    </div>
                <?php endif; ?> 
                <div class="help-block"></div>
            </div> 
        </div> 
    </div> 

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-attachment required fg-line">
            <?php for($count = 0;(!empty($model_master->attachments[$count]["destination_url"]) && count($model_master->attachments)>=$count);$count++){ ?>
                <?php if ($model_master->attachments[$count]["type"] == 'blob') : ?>
                            <ul>
                            <?php $destinationURL = $model_master->attachments[$count]["destination_url"]; $mimeType = $model_master->attachments[$count]["mimeType"];  $mimeType_blob_url = $mimeType.",".$destinationURL;   ?>
                                
                                    <li> <a href=<?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION);
                                                    if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'JPG') {
                                                        echo $destinationURL;
                                                    } else {
                                                        echo "javascript:void(0)";
                                                    }  ?> <?php $extension = pathinfo($destinationURL, PATHINFO_EXTENSION);
                                                    if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'JPG') {
                                                        echo "target='_blank'";
                                                    } else {
                                                        echo "onclick = saveFile('" . $mimeType_blob_url . "')";
                                                    }  ?>> <?php $filename = explode("/", $destinationURL);echo $filename[4]; ?> </a> </li>
                                
                            </ul>
                            <?php else: ?>
                            <ul>
                            <?php $filename = $model_master->attachments[$count]["destination_url"]   ?>
                                    <li> <a href="<?php echo "/files/" . $filename; ?>" <?php $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                                                                            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                                                                                                echo "target='_blank'";
                                                                                            } else {
                                                                                                echo 'download';
                                                                                            } ?>> <?php echo $filename; ?> </a> </li>    
                            </ul>
                        <?php endif; ?>
                        <?php }?>  
            </div>
        </div>
    </div>

<?php endif; ?>
<?php if ($model_master->isNewRecord && ( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_WT_SAFETY_PERSONNEL || Yii::$app->session->get('user.role_id') == ROLE_WT_EXECUTIVE_MANAGER)): ?>
        <div class="row hidden">
        <div class="col-sm-6">
            <div class="form-group field-newsflash required fg-line">
                <label class="checkbox checkbox-inline m-r-20">
                    <input type="checkbox" value="1" class="newsflash-checkbox" 
                               name="AppCase[newsflash_allowed]" onchange="onChangeNewsFlash(this)"  <?php echo ($model_master !== null && $model_master->newsflash_allowed == 1) ? "checked" : " disabled='disabled'" ?>><i
                           class="input-helper"></i>Send Newsflash?
                </label>

                    <div id="dvnewsflash" style="display: none; padding-top: 2%;" >
                        <label for="newsflash-emails-field" class="control-label">Enter the Email id's</label>
  <?=Html::textInput('newsflash-emails-field', '', array('class' => 'form-control', 'id' => 'newsflash-emails-field', 'onchange' => 'onChangenewflashinput(this)', 'placeholder' => 'Please enter comma seperated emails'))?>

                                       </div>
                                        <div id="div-news-flash-error" style="display: none">
                            <h5 style="font-size: 13px;color: #f44336;" > Please enter the valid email id's with comma seperated.</h5>
                        </div>
                                       <div id="div-news-flash-blank-error" style="display: none">
                            <h5 style="font-size: 13px;color: #f44336;" >Enter the Email id's cannot be blank.</h5>
                        </div>


                <div class="help-block"></div>
            </div>
        </div>
    </div>
<?php endif; ?> 

<?php endif; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="pull-left m-t-10">
            <label class="radio radio-inline m-r-20">
                <input type="radio" value="1"
                       name="AppCase[is_active]" <?= ( $model_master->is_active == 1 ) ? "checked" : "" ?> >
                <i class="input-helper"></i>
                Active
            </label>
            <label class="radio radio-inline m-r-20">
                <input type="radio" value="0"
                       name="AppCase[is_active]" <?= ( $model_master->is_active == 0 ) ? "checked" : "" ?> >
                <i class="input-helper"></i>
                Inactive
            </label>
        </div>
    </div>
        <?= Html::submitButton($model_master->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right send-button jsBeforeSubmitFormBtn']) ?>

        <?php if (($model_master->isNewRecord)) : ?>
        <a class="btn btn-warning pull-right" id="discardbutton" style="margin-right: 5px;">Discard</a>
         <?php endif; ?>  
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>
<!-- Babel polyfill, contains Promise -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.6.15/browser-polyfill.min.js"></script>
<!--FilePond polyfills from the CDN -->
<script src="https://unpkg.com/filepond-polyfill/dist/filepond-polyfill.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/safetycodesection.js"></script>
<?php if ($model_master->isNewRecord) : ?>

<?php $this->registerJsFile( "@web/js/AutoSaveIssue.js" ); ?>
<?php endif; ?>  
<script>
// Register plugins
FilePond.registerPlugin(
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType
            );

            // Set default FilePond options
            FilePond.setOptions({
                maxTotalFileSize: '20MB',
                labelMaxTotalFileSizeExceeded: '',
                allowFileTypeValidation: true,
                acceptedFileTypes: ['image/jpeg', 'image/png', 'image/heic', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                labelFileTypeNotAllowed: 'File of invalid type',
                labelMaxTotalFileSize: 'Maximum total file size is {filesize}.',
                fileValidateTypeLabelExpectedTypes: 'It supports only {allButLastType} or {lastType}.',
                fileValidateTypeLabelExpectedTypesMap: {
                    'image/jpeg': '.jpg',
                    'image/png': '.png',
                    'image/heic': '.heic',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/pdf': '.pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'image/heic': '.heic'
                },
                fileValidateTypeDetectType: validateType
            });

            var pond = FilePond.create(
                document.querySelector('.filepond'), {
                    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/heic', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ],
                    storeAsFile: true
                }
            );
  setInterval(function(){                
            $('#IssueForm').find('.has-error').each(function(index, element) {
              if($(this).length > 0){
                 $('#loader').hide();
                 $('.jsBeforeSubmitFormBtn').attr('disabled',false);
              }
               
            });
      },10);

$('#IssueForm').on('submit', function(){
  $('#loader').show();
            $('.jsBeforeSubmitFormBtn').attr('disabled',true);
             $('#IssueForm').find('.has-error').each(function(index, element) {
                  if($(this).length > 0){
                     $('#loader').hide();
                     $('.jsBeforeSubmitFormBtn').attr('disabled',false);
                  }
                   
                });

            setTimeout(function(){
               $('.jsBeforeSubmitFormBtn').attr('disabled',true);
                $('#IssueForm').find('.has-error').each(function(index, element) {
                  if($(this).length > 0){
                     $('#loader').hide();
                     $('.jsBeforeSubmitFormBtn').attr('disabled',false);
                  }
                   
                });
            },50);
       // });

});
if ($('#isincident').val() == "true"){
  $( "<label class='test'>Note: Do not enter the affected employee name in this field </label>" ).insertAfter( ".field-appcase-additional_information > .control-label" );
}

    if($('#input-affected_employee-display').val() == "" || $('#input-affected_employee-display').val() == "Choose an affected employee")
         $('#input-affected_employee-display').val("-Select the affected employee ID number or name-");
    // Jobsite
    var jobsiteIdChange = function ($val)
    {
        $selected_jobsite = $val;
        $('#appcase-contractor_id').val('');
        $selected_contractor = 0;
        $('#input-affected_employee-display').val("-Select the affected employee ID number or name-");
        $("#input-affected_employee-display").attr("data-original-title", "");
        $("#input-affected_employee-display").attr("data-content", "");
        $("#reptoff").val("false");
        $("#divoffenderuser").hide();
        //busco los contractors segun el jobsite elegido
        executeAjax
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-contractors-by-jobsite?id=') ?>" + $val
                        ).done(function (r) {
            if (r.length != 0) {
                var options = "<option value=''> -Select a contractor- </option>";
                for (var index in r) {
                    options += "<option value='" + index.trim() + "'>" + r[index] + "</option>";
                }
                $("#appcase-contractor_id").html(options);
            } else {
                $("#appcase-contractor_id").html("<option value=''> -Select a contractor- </option>");
            }
            return;
        });

        //busco los usuarios segun el jobsite
        executeAjax
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-jobsite?id=') ?>" + $val
                        ).done(function (r) {
            if (r.length != 0) {
                var options = "<option value=''> -Select the affected employee ID number or name- </option>";
                for (var i = 0, l = r.length; i < l; i++)
                {

//                        var label = $response[i][$name] + " - " + $response[i]["first_name"] + " " + $response[i]["last_name"]
                    options += "<option value='" + r[i]["id"] + "'>" + r[i]["employee_number"] + " - " + r[i]["first_name"] + " " + r[i]["last_name"] + "</option>";
                }
//                    for(var index in r){
//                        options += "<option value='" + index + "'>" + r[index] + "</option>";
//                    }
                $("#appcase-affected_user_id").html(options);
            } else {
                $("#appcase-affected_user_id").html("<option value=''> -Select the affected employee ID number or name-  </option>");
            }
            return;
        });

        //cargo los subjobsites segun el jobsite
        executeAjax
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-sub-jobsites?id=') ?>" + $val
                        ).done(function (r) {
            if (r.length != 0) {
                var options = "<option value=''> -Select a subjobsite- </option>";
                for (var i = 0, l = r.length; i < l; i++)
                {
                    options += "<option value='" + r[i]["id"] + "'>" + r[i]["subjobsite"] + "</option>";
                }
                $("#sub-jobsite-id-select").html(options);
            } else {
                $("#sub-jobsite-id-select").html("<option value=''> -Select a sub jobsite- </option>");
            }
            return;
        });

        onChangeDropdown
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-building?id=') ?>" + $val,
                        "#appcase-building_id",
                        "building",
                        "building",
                        buildingIdChange,
                        [""]
                        );

        // Verifico newsflash_allowed
        $.ajax
                ({
                    url: "<?= Yii::$app->urlManager->createUrl('ajax/check-newsflash?id=') ?>" + $val,
                    type: "POST",
                    dataType: "JSON",
                    success: function ($response)
                    {
                        //                $(".dropdownAModificar" ).
                        if ($response !== null && $response.newsflash_allowed == 1) {
                            $(".newsflash-checkbox").attr("disabled", false);
                        } else {
                            $(".newsflash-checkbox").attr("disabled", true);
                            $(".newsflash-checkbox").attr("checked", false);
                        }
                    }
                });


        $.ajax
                ({
                    url: "<?= Yii::$app->urlManager->createUrl('ajax/check-newsflash?id=') ?>" + $val,
                    type: "POST",
                    dataType: "JSON",
                    success: function ($response)
                    {
                        if ($response !== null && $response.photo_allowed == 1) {
                            $(".attachment-checkbox").attr("disabled", false);
                        } else {
                            $(".attachment-checkbox").attr("disabled", true);
                            $(".attachment-checkbox").attr("checked", false);
                        }
                    }
                });

        return false;
    };

    // Contractor
    var contractorIdChange = function ($contractor, $jobsite)
    {
        if ($contractor !== "" && $jobsite !== "") {

            $('#input-affected_employee-display').val("-Select the affected employee ID number or name-");
            $("#input-affected_employee-display").attr("data-original-title", "");
            $("#input-affected_employee-display").attr("data-content", "");
            $('#hdnAffectedUsr').val('');
             $("#appcase-affected_user_id").html("<option value=''> -Select the affected employee ID number or name-  </option>");
             $("#reptoff").val("false");
            $("#divoffenderuser").hide();
            $selected_contractor = $contractor;
            $selected_jobsite = $jobsite;
            <?php  $model_master->affected_user_id = null;   ?>
            //busco los usuarios segun el contractor y el jobsite
            executeAjax
                    (
                            "<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-contractor-and-jobsite?contractor=') ?>" + $contractor + "<?= '&jobsite=' ?>" + $jobsite
                            ).done(function (r) {
                if (r.length != 0) {
                    var options = "<option value=''> -Select the affected employee ID number or name- </option>";
                    for (var i = 0, l = r.length; i < l; i++)
                    {

                        //                        var label = $response[i][$name] + " - " + $response[i]["first_name"] + " " + $response[i]["last_name"]
                        options += "<option value='" + r[i]["id"] + "'>" + r[i]["employee_number"] + " - " + r[i]["first_name"] + " " + r[i]["last_name"] + "</option>";
                    }
                    //                    for(var index in r){
                    //                        options += "<option value='" + index + "'>" + r[index] + "</option>";
                    //                    }
                    $("#appcase-affected_user_id").html(options);
                } else {
                    $("#appcase-affected_user_id").html("<option value=''> -Select the affected employee ID number or name-  </option>");
                }
                return;
            });

            //busco los usuarios de rol foreman segun el contractor y el jobsite
            executeAjax
                    (
                            "<?= Yii::$app->urlManager->createUrl('ajax/get-foreman-by-contractor-and-jobsite?contractor=') ?>" + $contractor + "<?= '&jobsite=' ?>" + $jobsite
                            ).done(function (r) {
                if (r.length != 0) {
                    var options = "<option value=''> -Select the foreman responsible for the employee- </option>";
                    for (var i = 0, l = r.length; i < l; i++)
                    {

                        //                        var label = $response[i][$name] + " - " + $response[i]["first_name"] + " " + $response[i]["last_name"]
                        options += "<option value='" + r[i]["id"] + "'>" + r[i]["employee_number"] + " - " + r[i]["first_name"] + " " + r[i]["last_name"] + "</option>";
                    }
                    //                    for(var index in r){
                    //                        options += "<option value='" + index + "'>" + r[index] + "</option>";
                    //                    }
                    $("#foreman-id-select").html(options);
                } else {
                    $("#foreman-id-select").html("<option value=''> -Select the foreman responsible for the employee- </option>");
                }
                return;
            });
        } else {
            //busco los usuarios SOLO segun el jobsite
            executeAjax
                    (
                            "<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-jobsite?id=') ?>" + $jobsite
                            ).done(function (r) {
                if (r.length != 0) {
                    var options = "<option value=''> -Select the affected employee ID number or name- </option>";
                    for (var i = 0, l = r.length; i < l; i++)
                    {

                        //                        var label = $response[i][$name] + " - " + $response[i]["first_name"] + " " + $response[i]["last_name"]
                        options += "<option value='" + r[i]["id"] + "'>" + r[i]["employee_number"] + " - " + r[i]["first_name"] + " " + r[i]["last_name"] + "</option>";
                    }
                    //                    for(var index in r){
                    //                        options += "<option value='" + index + "'>" + r[index] + "</option>";
                    //                    }
                    $("#appcase-affected_user_id").html(options);
                } else {
                    $("#appcase-affected_user_id").html("<option value=''> -Select the affected employee ID number or name-  </option>");
                }
                return;
            });
        }
        return false;
    };
    // Safety Code
    var safetyCodeInputClick = function ()
    {
        getSafetyCodeTreeView
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-safety-code-tree-view') ?>",
                        "#safety-code-modal",
                        "#safety-code-tree-view-container",
                        "#input-safety-code-display",
                        "#appcase-app_case_sf_code_id"
                        );


        return false;
    };

    // Building
    var buildingIdChange = function ($val)
    {
        if ($val !== "")
        {
            if ($('#input-affected_employee-display').val() == "-Select the affected employee ID number or name-")

            {
            	$("#reptoff").val("false");
                $("#divoffenderuser").hide();
                $("#input-affected_employee-display").attr("data-original-title", "");
                $("#input-affected_employee-display").attr("data-content", "");
            } 
        }


        onChangeDropdown
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-floor?id=') ?>" + $val,
                        "#appcase-floor_id",
                        "Floor",
                        "floor",
                        floorIdChange,
                        [""],
                        "buildingChange"
                        );

        return false;
    };
    // Floor
    var floorIdChange = function ($val)
    {
        onChangeDropdown
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-area?id=') ?>" + $val,
                        "#area-id-select",
                        "area",
                        "area",
                        null,
                        [],
                        'area'
                        );
        return false;
    };
    //User Affected
    var affectedUserIdChange = function ($val)
    {
        selectFromDropdown
                (
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-contractor?id=') ?>" + $val,
                        "#appcase-contractor_id",
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-foreman?id=') ?>"
                        );
        return false;
    };

    //Check if safety code input was filled to remove error class.
    var checkSafetyCodeValue = function () {
        if (document.getElementById("input-safety-code-display").getAttribute("data-content") != "") {
            document.getElementById("input-safety-code-display").parentNode.className =
            document.getElementById("input-safety-code-display").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
            document.getElementById("input-safety-code-display").parentNode.className += " has-success";
            return false;
        }
        setTimeout(checkSafetyCodeValue, 500);
    }
    checkSafetyCodeValue();

    var showModalToAffectedEmployee = function (current_element_id) {
        var contractor = 0;
        var jobsite = 0;
        var isedit = <?php echo $isedit ?>;
        if (isedit === 1)
        {
            if ($selected_contractor !== 0)
            {
                contractor = $selected_contractor;
            }

            if ($selected_jobsite !== 0)
            {
                jobsite = $selected_jobsite;
            }

            var id = $('#user-by-contractor-id').val();
            $('#hdnAffectedUsr').val(id);
            if (contractor !== 0 && jobsite !== 0)
            {
                getAffectedEmployeeByContractorDropdown(
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-contractor-and-jobsite?contractor=') ?>" + contractor + "<?= '&jobsite=' ?>" + jobsite,
                        "#affected-employee-modal",
                        "#affected-employee-creator-dropdown-container",
                        "#btn-affected-employee-creator-container",
                        current_element_id,
                        "#appcase-affected_user_id",
                        "#input-affected_employee-display"
                        );
            }
            return false;
        } else
        {
            if ($('#appcase-contractor_id').val() === "")
            {
                contractor = 0;
            } else
            {
                contractor = $('#appcase-contractor_id').val();
                jobsite = $('#appcase-jobsite_id').val();

            }

            if (contractor !== 0 && jobsite !== 0)
            {
                getAffectedEmployeeByContractorDropdown(
                        "<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-contractor-and-jobsite?contractor=') ?>" + contractor + "<?= '&jobsite=' ?>" + jobsite,
                        "#affected-employee-modal",
                        "#affected-employee-creator-dropdown-container",
                        "#btn-affected-employee-creator-container",
                        current_element_id,
                        "#appcase-affected_user_id",
                        "#input-affected_employee-display"
                        );
            }
            return false;
        }
        return;
    };

    var onSelectAffectedEmployee = function () {
        var id = $('#user-by-contractor-id').val();
        var isedit = <?php echo $isedit ?>;
        if (isedit === 1)
        {
            if ($selected_jobsite !== 0)
            {
                var jobsite = $selected_jobsite;
            }
        } else
        {
            var jobsite = <?php echo $selected_jobsite ?>;
        }
       
        $('#hdnAffectedUsr').val(id);
        var $selectedEmployee = $('#user-by-contractor-id option:selected').attr('data-subtext');
        $('#input-affected_employee-display').val($selectedEmployee);

        var id = $('#user-by-contractor-id').val();
        $("#appcase-affected_user_id").val(id);
        $("#input-affected_employee-display").val($selectedEmployee);
        $("#input-affected_employee-display").attr("data-original-title", "Selected affected employee code:");
        $("#input-affected_employee-display").attr("data-content", $selectedEmployee);
        checkAffectedValue();

        if (id !== 0 || id !== null)
        {
            executeAjax
                    (
                            "<?= Yii::$app->urlManager->createUrl('ajax/get-issues-by-affected-user?affected_user_id=') ?>" + id + "<?= '&jobsite_id= ' ?>" + jobsite
                            ).done(function (r) {
                if (r.length != 0) {
                	$("#reptoff").val("true");
                    $('#divoffenderuser').show();
                } else
                {
                	$("#reptoff").val("false");
                    $('#divoffenderuser').hide();
                }
            })
        }
        return false;
    };

    var showoffernderUser = function ()
    {
        var id = $('#user-by-contractor-id').val();
        var isedit = <?php echo $isedit ?>;
        if (isedit === 1)
        {
            if ($selected_jobsite !== 0)
            {
                var jobsite = $selected_jobsite;
            }
        } else
        {
            var jobsite = <?php echo $selected_jobsite ?>;
        }
        var existe = 0;
        $.ajax
                ({
                    url: "<?= Yii::$app->urlManager->createUrl('ajax/get-issues-by-affected-user?affected_user_id=') ?>" + id + "<?= '&jobsite_id= ' ?>" + jobsite,
                    type: "POST",
                    async: false,
                    dataType: "JSON",
                    success: function (response)
                    {
                        existe = response.length > 0 ? 1 : 0;
                        if (existe) {
                            var html = "<table class='table table-hover'>";
                            html += "<thead><tr><th>Issue Id</th><th>Issue Type</th></tr></thead>";
                            html += "<tbody data-link='row' class='rowlink'>";
                            for (var i = 0; i < response.length; i++) {
                                html += "<tr data-key='" + response[i].id + "'>";
                                html += "<td class='id-column' >";
                                html += "<a href='/app-case/view?id=" + response[i].id + "' target='_blank'>";
                                html += response[i].id;
                                html += "</td>";
                                html += "<td class='type-column'>";
                                html += response[i].issue_type;
                                html += "</a>";
                                html += "</td>";
                                //html +="<td class='table-action-button'><a href='" + getBaseURL() + "app-case/view?id=" + response[i].id + "'><i class='md md-swap-horiz view-case'></i></a></td>";
                                html += "</tr>";
                            }
                            html += "</tbody>";
                            html += "</table>";
                            $("#offenderuserandissues").html(html);
                            $('#offenderuserandissues').val(existe);
                            $("#offenderuserandissues").niceScroll({
                                cursorcolor: 'rgba(0,0,0,0.5)',
                                cursorborder: 0,
                                cursorborderradius: 0,
                                cursorwidth: '5px',
                                bouncescroll: true,
                                mousescrollstep: 80

                            });
                        }
                    }
                });

        $("#offender-user").modal({show: true});
        return false;
    };

    var ShowAttachment = function ()
    {
        $('attachment-checkbox').change(function ()
        {
            if ($(this).is(':checked'))
            {
                $("div#fileattachment").show();
            } else
            {
                $("div#fileattachment").hide();
            }
        }
        )

    };

    var onChange = function (element) {
        if (element.checked)
        {
            $("#dvfileattachment").show();
        } else
        {
            $("#dvfileattachment").hide();
            
        }
    };
    //             $("#chkattachment").change(function () {
    //         if ($(this).is(":checked")) {
    //           $("#dvfileattachment").show();
    //       } else {
    //            $("#dvfileattachment").hide();
    //        }
    //      });
    //}
    var onChangeInEdit = function (element) {
        if (element.checked)
        {
            $("#dvfileattachmentinedit").show();
        } else
        {
            $("#dvfileattachmentinedit").hide();
        }
    };

    



     /* Show error message to affected employee  */
    var checkAffectedValue = function () {
        if (document.getElementById("input-affected_employee-display").getAttribute("data-content") != "") {
            document.getElementById("input-affected_employee-display").parentNode.className =
            document.getElementById("input-affected_employee-display").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
            document.getElementById("input-affected_employee-display").parentNode.className += "has-success";
            $('.affected-emp-error').empty().append('');
            //document.getElementsByClassName("help-block").style.display = "none";
            return false;
        }else if(document.querySelector('.field-appcase-affected_user_id').matches('.has-error') == true){
          document.getElementById("input-affected_employee-display").parentNode.className += " has-error";
          $('.affected-emp-error').empty().append('Affected Employee cannot be blank.');
        }
        setTimeout(checkAffectedValue, 500);
    };
    checkAffectedValue();

    /*recordable should be 'yes' when losttime is 'yes'*/
    var onLostTimeClick = function (event)
    { 
        if(event == 1)
        {    
            $('#appcaseincident-recordable').val(1);   
            $('#appcaseincident-recordable')[0].disabled = true;
        } else if($('#appcaseincident-is_lost_time').val() == 0 && $('#appcaseincident-is_dart').val() == 0)
        {
            $('#appcaseincident-recordable').val(0);
            $('#appcaseincident-recordable')[0].disabled = false;
        }
    };
    
    if(($('#appcaseincident-recordable').val() == 1) && ($('#appcaseincident-is_lost_time').val() == 1) && ($('#appcaseincident-is_dart').val() == 1) ){
        $('#appcaseincident-recordable')[0].disabled = true;
    }


var onDateChange = function(event)
{ 
     var today = new Date().toLocaleDateString();
     var selectedDate = new Date(event.value).toLocaleDateString();
     

if(new Date(selectedDate).getTime() > new Date(today).getTime())
     {
        $('#appcase-app_case_status_id')[0].disabled = false;
        if($('#appcase-app_case_status_id').val() === "2")
        {
           $('#appcase-app_case_status_id').val("");
        }
        } else if (new Date(attached_fileselectedDate).getTime() < new Date(today).getTime()) {
           $('#appcase-app_case_status_id').val("2");
           if($('#appcase-app_case_status_id').val() === "2")
           {
             $('#appcase-app_case_status_id')[0].disabled= true;
           }
        } else if (new Date(selectedDate).getTime() === new Date(today).getTime())
       {
          $('#appcase-app_case_status_id')[0].disabled = false;  
           if($('#appcase-app_case_status_id').val() === "2")
          {
           $('#appcase-app_case_status_id').val("");
          }
       }
  };


     var onChangeNewsFlash = function (element) {
        if (element.checked)
        {
            $("#dvnewsflash").show();

        } else
        {
            $("#dvnewsflash").hide();
            $("#div-news-flash-blank-error").hide();
            $("#div-news-flash-error").hide();
            document.getElementById("dvnewsflash").parentNode.className =
           document.getElementById("dvnewsflash").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
            //document.getElementById("newsflash-emails-field").required = false;
        }
    };

var newsflashemail = document.querySelector('input[name="newsflash-emails-field"]');
//newsflashemail.setCustomValidity('Please enter the email id\'s.');

var onChangenewflashinput = function(evt) {
 $("#div-news-flash-blank-error").hide();
 let valid  = validateEmailList(evt.value);
 if(valid)
 {
     $("#div-news-flash-error").hide();
     document.getElementById("dvnewsflash").parentNode.className =
     document.getElementById("dvnewsflash").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
     document.getElementById("dvnewsflash").parentNode.className += " has-success";
 }else{
    $("#div-news-flash-error").show();
    document.getElementById("dvnewsflash").parentNode.className =
     document.getElementById("dvnewsflash").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
     document.getElementById("dvnewsflash").parentNode.className += " has-error";
 }
};
function validateEmailList(raw){
var emails = raw.split(',');
var valid = true;
var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

for (var i = 0; i < emails.length; i++) {
    if( emails[i] === "" || !regex.test(emails[i].replace(/\s/g, ""))){
        valid = false;
    }
}
return valid;
}


function validateForm() {
    var sfcode = $('#appcase-app_case_sf_code_id').val();
    $('.osha-section_error').empty();
    if(sfcode == "")
    {
      $('.osha-section_error').append('Safety code cannot be empty');
      $('.field-osha-section').addClass('has-error');
      $('.field-osha-section').addClass('osha-errorclass' );
      return false;
    }else{
      $('.field-osha-section').removeClass('has-error');
    }
 /* var c=document.getElementsByClassName('newsflash-checkbox');
  var x = "";
  // document.getElementById("newsflash-emails-field").value;
   if (c.checked || c[0].checked) {
    if (x == "") {
    $("#div-news-flash-blank-error").show();
    $("#div-news-flash-error").hide();
     document.getElementById("dvnewsflash").parentNode.className =
     document.getElementById("dvnewsflash").parentNode.className.replace(/(?:^|\s)has-error(?!\S)/g, '');
     document.getElementById("dvnewsflash").parentNode.className += " has-error";
    return false;
  }else{
    $("#div-news-flash-blank-error").hide();
  }
  }*/

}
$( "#IssueForm" ).submit(function( event ) {
    $("#appcaseincident-recordable").prop( "disabled", false );
});
var filenames = [];
            pond.on('addfile',
                function(error, file) {
                    if (filenames.includes(file.filename)) {
                        error = {
                            main: 'duplicate',
                            sub: 'A file with the same name was already selected.'
                        }
                    }
                    if (error) {
                        handleFileError(error, file);
                        filenames.push(file.filename);
                    } else {
                        filenames.push(file.filename);
                        var err = document.querySelector(".filepond-error");
                        err.innerHTML = " ";
                    }
                });

            pond.on('removefile',
                function(error, file) {
                    var index = filenames.indexOf(file.filename);
                    filenames.splice(index, 1);
                });

            function handleFileError(error, file) {
                var err = document.querySelector(".filepond-error");
                err.innerHTML = "'" + file.filename + "', cannot be loaded.</br> " + error.sub;
                pond.removeFile(file);
            }

            // Download a file form a url.
            function saveFile(mimetype_blob_url) {

                var blob_mime_arr = mimetype_blob_url.split(",");
                var blob_url = blob_mime_arr[1];
                // Get file name from url.
                var filename = blob_url.substring(blob_url.lastIndexOf("/") + 1).split("?")[0];
                var xhr = new XMLHttpRequest();
                xhr.responseType = 'blob';
                xhr.onload = function() {
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(xhr.response); // xhr.response is a blob
                a.download = filename; // Set the file name.
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                delete a;
                };
                xhr.overrideMimeType(blob_mime_arr[0]);
                xhr.open('GET', blob_url);
                xhr.send();

            }

            function validateType(source, type) {
                const p = new Promise((resolve, reject) => {
                    if (source.name.toLowerCase().indexOf('.heic') !== -1) {

                        resolve('image/heic')
                    } else {
                        resolve(type)
                    }
                })

                return p
            }
</script>
