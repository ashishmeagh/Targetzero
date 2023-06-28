<?php

namespace app\controllers;

use app\helpers\functions;
use Yii;
use app\models\AppCase;
use app\models\AppCaseIncident;
use app\models\AppCaseObservation;
use app\models\AppCaseRecognition;
use app\models\AppCaseViolation;
use app\models\Comment;
use app\models\Import;
use app\models\searches\AppCase as AppCaseSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\notification;
use app\components\AccessRule;
use app\components\sqlRoleBuilder;
use app\models\User;
use yii\web\UploadedFile;
use app\components\attachment;
use app\components\jobsiteData;
use app\components\userData;

/**
 * AppCaseController implements the CRUD actions for AppCase model.
 */
class AppCaseController extends AllController {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

        ];
    }

    /**
     * Reroutes based on roles and permissions.
     * @return mixed
     */
    public function beforeAction($action) {
        if (Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
            return parent::beforeAction($action);
        } else if ($action->id != "update") {
            return parent::beforeAction($action);
        } else if (Yii::$app->session->get("user.role_id") == ROLE_SAFETY_CONTRACTOR || Yii::$app->session->get("user.role_id") == ROLE_WT_SAFETY_PERSONNEL || Yii::$app->session->get("user.role_id") == ROLE_WT_PERSONNEL) {
            return parent::beforeAction($action);
        } else if (Yii::$app->session->get("user.role_id") == ROLE_TRADE_PARTNER) {
            return parent::beforeAction($action);
        } else {
            return $this->redirect(array('app-case/index'));
        }
    }

    /**
     * Lists all AppCase models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER) {
            //echo("<script>console.log('inside trade partner: ".Yii::$app->session->get( 'user.role_id' )."');</script>");
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
            if ($filterByJobsite) {
                // echo("<script>console.log('FILTER IS SET: ".Yii::$app->session->get( "user.id" )."');</script>");
                $response = ' and ';
                $loggedInUser = Yii::$app->session->get("user.id");
                $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
                $filterByJobsite = $filterByJobsite . $response . '[app_case].[contractor_id] =' . $contractorID['contractor_id'];
            } else {
                $loggedInUser = Yii::$app->session->get("user.id");
                $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
                $filterByJobsite = '[app_case].[contractor_id] =' . $contractorID['contractor_id'];
            }
        } elseif (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_WT_EXECUTIVE_MANAGER) {
            $filterByJobsite = ' AND ';
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
        } elseif (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
            $filterByJobsite = '';
        } else {
            // $filterByJobsite = '';
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
        }

        $searchModel = new AppCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterByJobsite);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
                    'title' => "Issues"
        ]);
    }

    /**
     * Lists My AppCase models.
     * @return mixed
     */
    public function actionMyIssues() {
        if (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_WT_EXECUTIVE_MANAGER && Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER) {
            $filterByJobsite = ' AND ';
            $filterByJobsite .= sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
        } else {
            $filterByJobsite = '';
        }

        $searchModel = new AppCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'creator_id = ' . Yii::$app->session->get('user.id') . $filterByJobsite);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
                    'title' => "My issues"
        ]);

    }

    /**
     * Lists Recent AppCase models.
     * @return mixed
     */
    public function actionRecentIssues() {
        Yii::$app->session->set("processing", "false");
         $userid = Yii::$app->session->get("user.id");
         $typeid = (Yii::$app->session->get("deleteautosavedraft") != null) ? Yii::$app->session->get("deleteautosavedraft") : 0;

        if($typeid > 0){
         $sqlQuery = "Delete from [dbo].[app_case_draft] where typeid = $typeid AND userid = $userid";
       
         $data = Yii::$app->db->createCommand("$sqlQuery")->execute();
         Yii::$app->session->set("deleteautosavedraft", 0);
         return $this->redirect('recent-issues?page=1');
         
        } 
        
        if (Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER) {
            //echo("<script>console.log('inside trade partner: ".Yii::$app->session->get( 'user.role_id' )."');</script>");
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
            if ($filterByJobsite) {
                // echo("<script>console.log('FILTER IS SET: ".Yii::$app->session->get( "user.id" )."');</script>");
                $response = ' AND ';
                $loggedInUser = Yii::$app->session->get("user.id");
                $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
                $filterByJobsite = $filterByJobsite . $response . '[app_case].[contractor_id] =' . $contractorID['contractor_id'];
            } else {
                $loggedInUser = Yii::$app->session->get("user.id");
                $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
                $filterByJobsite = '[app_case].[contractor_id] =' . $contractorID['contractor_id'];
            }
        } elseif (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_WT_EXECUTIVE_MANAGER) {
            $filterByJobsite = ' AND ';
            $filterByJobsite .= sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
        } elseif (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) {
            $filterByJobsite = '';
        } else {
            // $filterByJobsite = '';
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId(Yii::$app->session->get('user.id'));
        }
        $searchModel = new AppCaseSearch();
        $date = date_format(date_modify(date_create(), "-1 month"), "Y-m-d H:i:s");

        if ($filterByJobsite) {
            if (Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER) {
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "app_case.updated > '" . $date . "'" . " AND " . $filterByJobsite);
            } elseif (Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_WT_EXECUTIVE_MANAGER) {
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "app_case.updated > '" . $date . "'" . "  " . $filterByJobsite);
            } else {
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "app_case.updated > '" . $date . "'" . " AND " . $filterByJobsite);
            }
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "app_case.updated > '" . $date . "'");
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
                    'title' => "Recent issues"
        ]);
    }

 /**
     * Lists Other Account AppCase models.
     * @return mixed
     */
    public function actionOtherAccountIssues() {

        $filterByJobsite ='';
        $filterBychilduser ='';
        $selectedchilduser='';
        $loggedInUser = Yii::$app->session->get("user.id");

       if(isset(Yii::$app->request->queryParams["otheraccount-employeeid"] ) && Yii::$app->request->queryParams["otheraccount-employeeid"] != "")
       {
         $selectedchilduser=Yii::$app->request->queryParams["otheraccount-employeeid"];
         $filterBychilduser = " and child_userid = ".Yii::$app->request->queryParams["otheraccount-employeeid"]; 
       }
        $sqlQuery = "select child_userid from [dbo].[merge_users] where status = 0 and parent_userid = ".$loggedInUser . $filterBychilduser;
     

       $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();

      // var_dump($usersList); exit();
        $Userids = '';

        $userListdataArray = array();
        foreach ($usersList as $key => $value) { 
          $Userids .= $value['child_userid'].',';
      }
      $Userids = rtrim($Userids, ',');

      $creatoridwheerecondition = "";
      if($Userids != '')
        $creatoridwheerecondition = 'creator_id in (' . $Userids .')';
      
     
         
        $searchModel = new AppCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,  $creatoridwheerecondition. $filterByJobsite,true);

        return $this->render('otheraccountissues', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'selectedemployee'=>$selectedchilduser,
                    'title' => "Other Account Issues"
        ]);
    }

    /**
     * Lists RepeatOffenderIssues
     * @return mixed
     */
    public function actionRepeatOffenderIssues($afid,$jid) {
   
        $userData = userData::getProfileById($afid);
        
        $searchModel = new AppCaseSearch();
        $dataProvider = $searchModel->GetRepeatoffenderissues(Yii::$app->request->queryParams,$afid,$jid);

        return $this->render('repeatoffenderissues', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'selectedemployee'=>$selectedchilduser,
            'ownerValue'=>'',
            'ownerId'=>'',
            'contractorValue'=>'',
            'contractorId'=>'',
            'jobsiteValue'=>'',
            'jobsiteId'=>'',
                    'title' => "Repeat Offender Issues - ".$userData['employee_number']." ".$userData['first_name']." ".$userData['last_name']
        ]);
    }

    /**
     * Displays associated OtherAccountIssues.
     *
     * @param integer $id App Case ID
     * @param string $from From where the view was called
     *
     * @return mixed
     */
    public function actionOtherAccountIssuesView($id, $from = NULL) {
        
        //app-case
        $model = $this->findModel($id);
        $timezone = $model->jobsite->timezone->timezone_code;
        $jobsite_timezone = new \DateTimeZone($timezone);
        $dateCreated = new \DateTime('now', $jobsite_timezone);
        $created = $dateCreated->format('Y/m/d H:i:s');
        $updated = $created;
        $model->attachments = attachment::getAttachmentsForWeb($id);
        $followerUserlist = $this->getFollowersList(Yii::$app->session->get("user.id"));
        $is_photoAllowed = jobsiteData::photoAllowed($model->id);
    
        //$model->created = functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone);
        // $model->updated = functions::convertFromTimezone($model->updated, SERVER_TIMEZONE, $timezone);
        //$model->updated = functions::convertFromTimezone($model->updated, SERVER_TIMEZONE, $timezone);

        //app-case-type
        switch ($model->app_case_type_id) {
            case APP_CASE_VIOLATION:
                $model_type = AppCaseViolation::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_RECOGNITION:
                $model_type = AppCaseRecognition::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_INCIDENT:
                $model_type = AppCaseIncident::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_OBSERVATION:
                $model_type = AppCaseObservation::find()->where(['app_case_id' => $model->id])->one();
                break;
        }
        //comment
        $model_comment = new comment();

        if ($model->app_case_type_id == APP_CASE_INCIDENT) {
            if (!isset($model_comment->report_type_id)) {
                $model_comment->report_type_id = $model_type->report_type_id;
                $model_comment->causation_factor = $model_type->causation_factor;
            }
        }

        $comments = Comment::find()->with('user')->with('reportType')->where(['app_case_id' => $id])->all();


/*Se eliminó la conversión del horario entre timezones.
Ahora siempre se graba la hora en el timezone del jobsite y de esa forma no se hacen conversiones*/
        /*$commentsTimezoned = array();
        if(count($comments) > 0){
        foreach($comments as $comment){
        $comment->created = functions::convertFromTimezone($comment->created, SERVER_TIMEZONE, $timezone);
        $commentsTimezoned[] = $comment;
        }
        }
        $comments = $commentsTimezoned;*/
          $post = Yii::$app->request->post();

        if ($model_comment->load(Yii::$app->request->post())) {
            //            $notified = false;
            $model_comment->is_active = 1;
            $model_comment->app_case_id = $id;
            $model_comment->user_id = Yii::$app->session->get("user.id");
            $model_comment->created = $created;
            $gmt_timezone = new \DateTimeZone('UTC');
            $gmtDate = new \DateTime('now', $gmt_timezone);
            $gmtUpdatetimeformat = $gmtDate->format('Y/m/d H:i:s');
            $model_comment->updated = $gmtUpdatetimeformat;
            $model->updated_gmt = $gmtUpdatetimeformat;
            $model->updated = $updated;
            /*$model->updated = functions::convertFromTimezone($model_comment->updated, SERVER_TIMEZONE, $timezone);*/

            if ($model->app_case_type_id == APP_CASE_INCIDENT) {

                if (isset($post['Comment']['recordable']) || isset($post['Comment']['is_property_damage']) || isset($post['Comment']['is_lost_time'])|| isset($post['Comment']['is_dart']) || isset($post['Comment']['causation_factor'])) {
                    $new_report_type = $post['Comment']['report_type_id'];
                    $model_type->recordable = $post['Comment']['recordable'];
                    $model_type->is_property_damage = $post['Comment']['is_property_damage'];
                    $model_type->is_lost_time = $post['Comment']['is_lost_time'];
                    $model_type->is_dart = $post['Comment']['is_dart'];
                    if (isset($post['Comment']['causation_factor'])) {
                        $model_type->causation_factor = $post['Comment']['causation_factor'];
                    } else {
                        $model_type->causation_factor = null;
                    }

                    $model_type->report_type_id = $new_report_type;
                    if ($new_report_type == APP_CASE_INCIDENT_FINAL) {
                        $model->app_case_status_id = APP_CASE_STATUS_CLOSE;
                        //                    notification::notifyClose($id);
                        //                    $notified = true;
                    } else {
                        $model->app_case_status_id = APP_CASE_STATUS_OPEN;
                    }

                    $model_type->save();
                }
            } else {
                $model_comment->recordable = 0;
                $model_comment->is_property_damage = 0;
                $model_comment->is_lost_time = 0;
                $model_comment->causation_factor = null;
            }
            $model->save();
            //Skip notification flag (if true, do not send notifications)
            $skipNotification = isset($post['skip']) && $post['skip'];
            $status = $model_comment->save();
            // print_r($model_comment->getErrors());
            if ($model_comment->save()) {
                //                $notified == false ? notification:notifyComment($id): null;
                $user_id = Yii::$app->session->get('user.id');
                if (!$skipNotification) {
                    notification::notifyComment($id, $user_id);
                }

                return $this->redirect([
                    'otheraccountview',
                    'id' => $id,
                    'model_type' => $model_type,
                ]);
            } else {
                return $this->render('otheraccountview', [
                    'model' => $model,
                    'created' => $model->created,
                    /*'created'       => functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone),*/
                    'model_type' => $model_type,
                    'model_comment' => $model_comment,
                    'comments' => $comments,
                    'timezone' => $timezone,
                    'followerUserlist' => $followerUserlist,
                    'Isattachmentenable' => $is_photoAllowed,
                ]);
            }
        } else if(isset($post['newsflash-user']['is_active'])){
             
              $newsFlashUsers = isset($post['newsflash-users']) ? $post['newsflash-users'] : null;
              $newsFlashCustomEmails = isset($post['newsflash-emails-field']) ? $post['newsflash-emails-field'] : null;
             notification::addCustomFollowers($id, $newsFlashCustomEmails);
             notification::newsflashNewVersion($id, null,$newsFlashUsers, $newsFlashCustomEmails,$model->attachments);

             Yii::$app->session->setFlash('success', "Safety Alert has been sent successfully");

             return $this->render('otheraccountview', [
                'model' => $model,
                'created' => $model->created,
                'model_type' => $model_type,
                'model_comment' => $model_comment,
                'comments' => $comments,
                'timezone' => $timezone,
                'followerUserlist' => $followerUserlist,
                'Isattachmentenable' => $is_photoAllowed,
                ]);
            }else if(isset($_FILES['attachment']["name"])){
 
                if (!empty($_FILES['attachment']["name"][0])) {
                     $creator_id = Yii::$app->session->get("user.id");
                    $count = 0;
                    $destinationURL= array();
                    while(count($_FILES['attachment']["name"]) > $count){
                        $filetoUpload = $_FILES['attachment']["tmp_name"][$count];
                        $blobName = str_replace(' ', '', $_FILES['attachment']["name"][$count]);
                        $blobResponse[$count] = attachment::uploadBlob($filetoUpload, $blobName, $model->id);
                        $count++;  
                    }
                    
                    foreach($blobResponse as $blobStatus){
                        if($blobStatus['success'] == TRUE || $blobStatus["success"] == 1|| $blobStatus["success"] == 'true'){
                            $destinationURL['destinationURL'][$count] =  $blobStatus['destinationURL'];
                            $destinationURL['mimeType'][$count] =  $blobStatus['mimeType'];
                            $count++;
                        }  
                    }
                    if(count($destinationURL) > 0){
                        $response = attachment::SaveAttachmentsForWeb($model->id, $creator_id, $destinationURL);

                    if ($response["success"] == 1 || $response["success"] == "true" || $response["success"] == 'true') {
                            $reptoffeder = false;
                            $searchModel = new AppCaseSearch();
                            $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
                            $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
                            notification::notifyNewWithAttachment($model->id, $model->newsflash_allowed, false, $reptoffeder, $destinationURL);
                         Yii::$app->session->setFlash('success', "attachment has been add successfully");

                    }
                    }else{
                        Yii::$app->session->setFlash('success', "Attachment already exists");
                    }    
                }
              return $this->redirect([
                    'otheraccountview',
                    'id' => $id,
                    'model_type' => $model_type,
                ]);
            }
        else {
            return $this->render('otheraccountview', [
                'model' => $model,
                'created' => $model->created,
                /*'created'       => functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone),*/
                'model_type' => $model_type,
                'model_comment' => $model_comment,
                'comments' => $comments,
                'timezone' => $timezone,
                'followerUserlist' => $followerUserlist,
                'Isattachmentenable' => $is_photoAllowed,
            ]);
        }

    }

    /**
     * Displays a single AppCase model.
     *
     * @param integer $id App Case ID
     * @param string $from From where the view was called
     *
     * @return mixed
     */
    public function actionView($id, $from = NULL) {
        $user_id = Yii::$app->session->get("user.id");
        if ($from == "newsflash") {
            $user_id = Yii::$app->session->get("user.id");
            // mark as viewed
            Yii::$app->db->createCommand("UPDATE notification SET is_read = '1' WHERE user_id = '$user_id' AND app_case_id = '$id'")->execute();
        }
        //app-case
        $model = $this->findModel($id);
        $timezone = $model->jobsite->timezone->timezone_code;
        $jobsite_timezone = new \DateTimeZone($timezone);
        $dateCreated = new \DateTime('now', $jobsite_timezone);
        $created = $dateCreated->format('Y/m/d H:i:s');
        $updated = $created;
        $model->attachments = attachment::getAttachmentsForWeb($id);
        $followerUserlist = $this->getFollowersList(Yii::$app->session->get("user.id"));
        $is_photoAllowed = jobsiteData::photoAllowed($model->id);
        $jobsite_id =  $model->jobsite_id;
        $affected_user_id =  $model->affected_user_id;
        
        
    
        //$model->created = functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone);
        // $model->updated = functions::convertFromTimezone($model->updated, SERVER_TIMEZONE, $timezone);
        //$model->updated = functions::convertFromTimezone($model->updated, SERVER_TIMEZONE, $timezone);

        //app-case-type
        switch ($model->app_case_type_id) {
            case APP_CASE_VIOLATION:
                $model_type = AppCaseViolation::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_RECOGNITION:
                $model_type = AppCaseRecognition::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_INCIDENT:
                $model_type = AppCaseIncident::find()->where(['app_case_id' => $model->id])->one();
                break;
            case APP_CASE_OBSERVATION:
                $model_type = AppCaseObservation::find()->where(['app_case_id' => $model->id])->one();
                break;
        }
        //comment
        $model_comment = new comment();

        if ($model->app_case_type_id == APP_CASE_INCIDENT) {
            if (!isset($model_comment->report_type_id)) {
                $model_comment->report_type_id = $model_type->report_type_id;
                $model_comment->causation_factor = $model_type->causation_factor;
            }
        }

        $comments = Comment::find()->with('user')->with('reportType')->where(['app_case_id' => $id])->all();


/*Se eliminó la conversión del horario entre timezones.
Ahora siempre se graba la hora en el timezone del jobsite y de esa forma no se hacen conversiones*/
        /*$commentsTimezoned = array();
        if(count($comments) > 0){
        foreach($comments as $comment){
        $comment->created = functions::convertFromTimezone($comment->created, SERVER_TIMEZONE, $timezone);
        $commentsTimezoned[] = $comment;
        }
        }
        $comments = $commentsTimezoned;*/
          $post = Yii::$app->request->post();

        if ($model_comment->load(Yii::$app->request->post())) {
            //            $notified = false;
            $model_comment->is_active = 1;
            $model_comment->app_case_id = $id;
            $model_comment->user_id = Yii::$app->session->get("user.id");
            $model_comment->created = $created;
            $gmt_timezone = new \DateTimeZone('UTC');
            $gmtDate = new \DateTime('now', $gmt_timezone);
            $gmtUpdatetimeformat = $gmtDate->format('Y/m/d H:i:s');
            $model_comment->updated = $gmtUpdatetimeformat;
            $model->updated_gmt = $gmtUpdatetimeformat;
            $model->updated = $updated;
            /*$model->updated = functions::convertFromTimezone($model_comment->updated, SERVER_TIMEZONE, $timezone);*/

            if ($model->app_case_type_id == APP_CASE_INCIDENT) {

                if (isset($post['Comment']['recordable']) || isset($post['Comment']['is_property_damage']) || isset($post['Comment']['is_lost_time']) || isset($post['Comment']['is_dart']) || isset($post['Comment']['causation_factor'])) {
                    $new_report_type = $post['Comment']['report_type_id'];
                    $model_type->recordable = $post['Comment']['recordable'];
                    $model_type->is_property_damage = $post['Comment']['is_property_damage'];
                    $model_type->is_lost_time = $post['Comment']['is_lost_time'];
                    $model_type->is_dart = $post['Comment']['is_dart'];
                    if (isset($post['Comment']['causation_factor'])) {
                        $model_type->causation_factor = $post['Comment']['causation_factor'];
                    } else {
                        $model_type->causation_factor = null;
                    }

                    $model_type->report_type_id = $new_report_type;
                    if ($new_report_type == APP_CASE_INCIDENT_FINAL) {
                        $model->app_case_status_id = APP_CASE_STATUS_CLOSE;
                        //                    notification::notifyClose($id);
                        //                    $notified = true;
                    } else {
                        $model->app_case_status_id = APP_CASE_STATUS_OPEN;
                    }

                    $model_type->save();
                }
            } else {
                $model_comment->recordable = 0;
                $model_comment->is_property_damage = 0;
                $model_comment->is_lost_time = 0;
                $model_comment->causation_factor = null;
            }
            $model->save();
            //Skip notification flag (if true, do not send notifications)
            $skipNotification = isset($post['skip']) && $post['skip'];
            $status = $model_comment->save();
            // print_r($model_comment->getErrors());
            if ($model_comment->save()) {
                //                $notified == false ? notification:notifyComment($id): null;
                
                if (!$skipNotification) {
                    $reptoffeder = false;
                    $searchModel = new AppCaseSearch();
                    $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
                    $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
                    notification::notifyComment($id, $user_id, $reptoffeder);
                }

                return $this->redirect([
                    'view',
                    'id' => $id,
                    'model_type' => $model_type,
                ]);
            } else {
                return $this->render('view', [
                    'model' => $model,
                    'created' => $model->created,
                    /*'created'       => functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone),*/
                    'model_type' => $model_type,
                    'model_comment' => $model_comment,
                    'comments' => $comments,
                    'timezone' => $timezone,
                    'followerUserlist' => $followerUserlist,
                    'Isattachmentenable' => $is_photoAllowed,
                ]);
            }
        } else if(isset($post['newsflash-user']['is_active'])){
             
             $reptoffeder = false;
             $searchModel = new AppCaseSearch();
             $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
             $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
             $newsFlashUsers = isset($post['newsflash-users']) ? $post['newsflash-users'] : null;
             $newsFlashCustomEmails = isset($post['newsflash-emails-field']) ? $post['newsflash-emails-field'] : null;
             notification::addCustomFollowers($id, $newsFlashCustomEmails);
             notification::newsflashNewVersion($id, null,$newsFlashUsers, $newsFlashCustomEmails,$model->attachments, $reptoffeder);

             Yii::$app->session->setFlash('success', "Safety Alert has been sent successfully");

             return $this->render('view', [
                'model' => $model,
                'created' => $model->created,
                'model_type' => $model_type,
                'model_comment' => $model_comment,
                'comments' => $comments,
                'timezone' => $timezone,
                'followerUserlist' => $followerUserlist,
                'Isattachmentenable' => $is_photoAllowed,
                ]);
            }else if(isset($_FILES['attachment']["name"])){
 
                if (!empty($_FILES['attachment']["name"][0])) {
                    
                     $creator_id = Yii::$app->session->get("user.id");
                     $count = 0;
                    $destinationURL= array();

                    while(count($_FILES['attachment']["name"]) > $count){
                        $filetoUpload = $_FILES['attachment']["tmp_name"][$count];
                        $blobName = str_replace(' ', '', $_FILES['attachment']["name"][$count]);
                        $blobResponse[$count] = attachment::uploadBlob($filetoUpload, $blobName, $model->id);
                        $count++;  
                    }

                    foreach($blobResponse as $blobStatus){
                        if($blobStatus['success'] == TRUE || $blobStatus["success"] == 1|| $blobStatus["success"] == 'true'){
                            $destinationURL[$count]['destinationURL'] =  $blobStatus['destinationURL'];
                            $destinationURL[$count]['mimeType'] =  $blobStatus['mimeType'];
                            $count++;
                        }  
                    }
                    if(count($destinationURL) > 0){
                        $response = attachment::SaveAttachmentsForWeb($model->id, $creator_id, $destinationURL);

                    if ($response["success"] == 1 || $response["success"] == "true" || $response["success"] == 'true') {
                        
                            $reptoffeder = false;
                            $searchModel = new AppCaseSearch();
                            $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
                            $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
                            
                            notification::notifyNewWithAttachment($model->id, $model->newsflash_allowed, false, $reptoffeder, $destinationURL);
                            Yii::$app->session->setFlash('success', "Attachment has been added successfully");

                    }
                    }else{
                        Yii::$app->session->setFlash('success', "Attachment already exists");
                    }
                }

            
              return $this->redirect([
                    'view',
                    'id' => $id,
                    'model_type' => $model_type,
                ]);
            }
        else {
            return $this->render('view', [
                'model' => $model,
                'created' => $model->created,
                /*'created'       => functions::convertFromTimezone($model->created, SERVER_TIMEZONE, $timezone),*/
                'model_type' => $model_type,
                'model_comment' => $model_comment,
                'comments' => $comments,
                'timezone' => $timezone,
                'followerUserlist' => $followerUserlist,
                'Isattachmentenable' => $is_photoAllowed,
            ]);
        }

    }

    /**
     * Creates a new AppCase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $type Issue type
     * @return mixed
     */
    public function actionCreate($type) {
        // App Case
        $model_master = new AppCase();
        $model_master->app_case_type_id = $type;
        $model_master->creator_id = Yii::$app->session->get("user.id");
        $model_master->is_active = 1;
        $model_master->newsflash_allowed = 0;
        $model_master->photo_allowed = 0;
        $model_master->platform_id = 1;

        // App Case Violation
        if (APP_CASE_VIOLATION == $type) {
            $model_detail = new AppCaseViolation();
        }

        // App Case Recognition
        if (APP_CASE_RECOGNITION == $type) {
            $model_detail = new AppCaseRecognition();
        }

        // App Case Observation
        if (APP_CASE_OBSERVATION == $type) {
            $model_detail = new AppCaseObservation();
        }

        // App Case Incident
        if (APP_CASE_INCIDENT == $type) {
            $model_detail = new AppCaseIncident();
        }
        
        $model_master_post_load = $model_master->load(Yii::$app->request->post());
        $model_detail_post_load = $model_detail->load(Yii::$app->request->post());

        // Dated
        if (APP_CASE_INCIDENT == $type) {
            $incident_time_typed_value = $model_detail->incident_datetime;
            $model_detail->incident_datetime = $this->dateFormat($model_detail->incident_datetime);
        } else {
            $model_detail->correction_date = $this->dateFormat($model_detail->correction_date);
        }

        if ($model_master_post_load && $model_detail_post_load && Yii::$app->session->get("processing") == "false") {
            date_default_timezone_set("UTC");
            $model_master->updated_gmt = date("Y-m-d H:i:s");
            //"Created" and "Updated" fields are updated with the current time in Jobsite's time zone.
            $jobsiteTimezone = $model_master->jobsite->timezone->timezone_code;
            date_default_timezone_set($jobsiteTimezone);
            $model_master->created = date("Y/m/d H:i:s");
            $model_master->updated = $model_master->created;
            // Init Transaction
            Yii::$app->session->set("processing", "true");
            $trasanction = $model_master->getDb()->beginTransaction();

            if (empty($_POST['hdnAffectedUsr'])) {
                $model_master->affected_user_id = null;
            }

            if (!empty($_FILES['attachment']["name"][0])) {
                $model_master->is_attachment = 1;
            } else {
                $model_master->is_attachment = 0;
            }

            if (APP_CASE_RECOGNITION == $type) {
                $model_master->app_case_status_id = 2;
            }
            
            $reptoffeder = false;
            if ($type == APP_CASE_VIOLATION){
                //To add repeat offender
            $reptoffeder = ($_POST['reptoff'] == "true") ? true: false;
            }
            


            // Dated
            if (APP_CASE_INCIDENT == $type) {
                if ($model_detail->incident_time == "") {
                    $incident_time = $model_master->created;
                } else {
                    $incident_time = $this->dateFormatToSQL($model_detail->incident_date . ' ' . $model_detail->incident_time);
                }
                if ($model_detail->is_lost_time == 1) {
                    $model_detail->recordable = 1;
                }
                $model_detail->incident_datetime = $incident_time;
            } else {
                if ($model_detail->correction_date == "") {
                    $correction_date = $model_master->created;
                } else {
                    $correction_date = $this->dateFormatToSQL($model_detail->correction_date);
                }
                $model_detail->correction_date = $correction_date; //$this->dateFormatToSQL( $model_detail->correction_date );
                if (APP_CASE_VIOLATION == $type) {
                    $selectedCreateDate = new \DateTime($model_master->created);
                    $createdDate = $selectedCreateDate->format('Y-m-d');
                    $selectedCorrectionDate = new \DateTime($model_detail->correction_date);
                    $correctionDate = $selectedCorrectionDate->format('Y-m-d');
                    if ($createdDate > $correctionDate) {
                        $model_master->app_case_status_id = 2;
                    } else if ($createdDate < $correctionDate) {

                    } else if ($createdDate == $correctionDate) {

                    }
                }
            }
            $model_master_status = $model_master->save();
            // Detail
            $model_detail->app_case_id = $model_master->id;

            $model_detail_status = $model_detail->save();
            if ($model_master_status && $model_detail_status) {

                $trasanction->commit();
                notification::addFollowers($model_master->id);
                if (isset($_POST['newsflash-emails-field'])) {
                    notification::addCustomFollowers($model_master->id, $_POST['newsflash-emails-field']);
                }

                $is_photoAllowed = jobsiteData::photoAllowed($model_master->id);

                if ($is_photoAllowed && !empty($_FILES['attachment']["name"]) && ($_FILES['attachment']["name"][0] != "") ) {
                    $count = 0;

                    while(count($_FILES['attachment']["name"]) > $count){
                        $filetoUpload = $_FILES['attachment']["tmp_name"][$count];
                        $blobName = str_replace(' ', '', $_FILES['attachment']["name"][$count]);
                        $blobResponse[$count] = attachment::uploadBlob($filetoUpload, $blobName, $model_master->id);
                        $count++;  
                    }

                    foreach($blobResponse as $blobStatus){
                        if($blobStatus['success'] == TRUE || $blobStatus["success"] == 1|| $blobStatus["success"] == 'true'){
                            $destinationURL[$count]['destinationURL'] =  $blobStatus['destinationURL'];
                            $destinationURL[$count]['mimeType'] =  $blobStatus['mimeType'];
                            $count++;
                        }  
                    }

                    $response = attachment::SaveAttachmentsForWeb($model_master->id, $model_master->creator_id, $destinationURL);

                    if ($response["success"] == 1 || $response["success"] == "true" || $response["success"] == 'true') {
                        notification::notifyNewWithAttachment($model_master->id, $model_master->newsflash_allowed, true, $reptoffeder, $destinationURL);
                    }
                } else {
                    notification::notifyNew($model_master->id, $model_master->newsflash_allowed, true, $reptoffeder);
                }

                if (isset(Yii::$app->request->post()["AppCase"]["newsflash_allowed"]) && Yii::$app->request->post()["AppCase"]["newsflash_allowed"] == "1") {
                    if (!empty($_FILES['attachment']["name"])) {
                        notification::newsflashWithAttachment($model_master->id, true);
                    } else {
                        notification::newsflash($model_master->id, true);
                    }
                }
                 $userid = Yii::$app->session->get("user.id");
                $sqlQuery = "Delete from [dbo].[app_case_draft] where typeid = $type AND userid = $userid";
       
                $data = Yii::$app->db->createCommand("$sqlQuery")->execute();
                Yii::$app->session->set("deleteautosavedraft", $type );
                return $this->redirect('recent-issues?page=1');
            } else {
                return $this->render('create', [
                    'model_master' => $model_master,
                    'model_detail' => $model_detail,
                ]);
            }
        } elseif (Yii::$app->session->get("processing") == "true") {
            return $this->redirect('recent-issues?page=1');
        } else {
            return $this->render('create', [
                'model_master' => $model_master,
                'model_detail' => $model_detail,
            ]);
        }
    }

    /**
     * Updates an existing AppCase model.
     * If update is successful, will be redirected to the 'view' page.
     *
     * @param integer $id Issue ID
     *
     * @return mixed
     */
    public function actionUpdate($id) {
        // App Case
        $post = Yii::$app->request->post();
        $model_master = $this->findModel($id);
        $prev_status = $model_master->app_case_status_id;
        $model_master->attachments = attachment::getAttachmentsForWeb($id);
        $followerUserlist = $this->getFollowersList(Yii::$app->session->get("user.id"));
        $jobsite_id = $model_master->jobsite_id;
        $affected_user_id =  $model_master->affected_user_id;
        

        // App Case Violation
        if (APP_CASE_VIOLATION == $model_master->app_case_type_id) {
            $model_detail = AppCaseViolation::find()->where(["app_case_id" => $id])->one();
        }

        // App Case Recognition
        if (APP_CASE_RECOGNITION == $model_master->app_case_type_id) {
            $model_detail = AppCaseRecognition::find()->where(["app_case_id" => $id])->one();
        }

        // App Case Observation
        if (APP_CASE_OBSERVATION == $model_master->app_case_type_id) {
            $model_detail = AppCaseObservation::find()->where(["app_case_id" => $id])->one();
        }

        // App Case Incident
        if (APP_CASE_INCIDENT == $model_master->app_case_type_id) {
            $model_detail = AppCaseIncident::find()->where(["app_case_id" => $id])->one();
/*                $timezone = $model_master->jobsite->timezone->timezone_code;
$model_detail->incident_datetime = functions::convertFromTimezone($model_detail->incident_datetime, SERVER_TIMEZONE, $timezone);
             */
        }

        $model_master_post_load = $model_master->load(Yii::$app->request->post());
        $model_detail_post_load = $model_detail->load(Yii::$app->request->post());

        if ($model_master_post_load && $model_detail_post_load) {
            //Skip notification flag (if true, notifications are sent)
            //$skipNotification = isset(Yii::$app->request->post()['skip']) && Yii::$app->request->post()['skip'];

         $skipNotification = isset($post['skip']) && $post['skip'];

            date_default_timezone_set("UTC");
            $model_master->updated_gmt = date("Y-m-d H:i:s");
            //"Updated" field is updated with the current time in Jobsite's time zone.
            $jobsiteTimezone = $model_master->jobsite->timezone->timezone_code;
            date_default_timezone_set($jobsiteTimezone);
            $model_master->updated = date("Y-m-d H:i:s");

            // Init Transaction
            $transaction = $model_master->getDb()->beginTransaction();
            $model_master->affected_user_id = $_POST['hdnAffectedUsr'];
            
            if (!empty($_FILES['attachment']["name"][0])) {
                $model_master->is_attachment = 1;
            } else {
                $model_master->is_attachment = 0;
            }

            if (APP_CASE_VIOLATION == $model_master->app_case_type_id) {
                $selectedCreateDate = new \DateTime($model_master->created);
                $createdDate = $selectedCreateDate->format('Y-m-d');
                $selectedCorrectionDate = new \DateTime($model_detail->correction_date);
                $correctionDate = $selectedCorrectionDate->format('Y-m-d');
                if ($createdDate > $correctionDate) {
                    $model_master->app_case_status_id = 2;
                } else if ($createdDate < $correctionDate) {

                } else if ($createdDate == $correctionDate) {

                }
            }

            $model_master_status = $model_master->save();

            //check status change
            if ($prev_status != APP_CASE_STATUS_CLOSE && $model_master->app_case_status_id == APP_CASE_STATUS_CLOSE) {
                $user_id = Yii::$app->session->get('user.id');
              
                if (!$skipNotification) {
                    notification::notifyClose($model_master->id, $user_id);
                }
            }

            // Dated
            if (APP_CASE_INCIDENT == $model_master->app_case_type_id) {
                $model_detail->incident_datetime = $this->dateFormatToSQL($model_detail->incident_date . ' ' . $model_detail->incident_time);
                /*$timezone = $model_master->jobsite->timezone->timezone_code;
            $model_detail->incident_datetime = functions::convertFromTimezone($model_detail->incident_datetime, $timezone, SERVER_TIMEZONE);
                 */
            } else {
                $model_detail->correction_date = $this->dateFormatToSQL($model_detail->correction_date);
            }
            // Detail
            $model_detail->app_case_id = $model_master->id;
            $model_detail_status = $model_detail->save();
            $user_id = Yii::$app->session->get('user.id');
            $reptoffeder = false;
            $searchModel = new AppCaseSearch();
            $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
            $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;
            if ($model_master_status && $model_detail_status) {
                $transaction->commit();

                $sqlQuery = "Delete from [dbo].[follower] where app_case_id = $model_master->id";
       
                $data = Yii::$app->db->createCommand("$sqlQuery")->execute();
                
                 notification::addFollowers($model_master->id);
                if (!$skipNotification)
                {
                $is_photoAllowed = jobsiteData::photoAllowed($model_master->id);

                if ($is_photoAllowed && !empty($_FILES['attachment']["name"][0])) {
                    $count = 0;
                    $destinationURL= array();

                    while(count($_FILES['attachment']["name"]) > $count){
                        $filetoUpload = $_FILES['attachment']["tmp_name"][$count];
                        $blobName = str_replace(' ', '', $_FILES['attachment']["name"][$count]);
                        $blobResponse[$count] = attachment::uploadBlob($filetoUpload, $blobName, $model_master->id);
                        $count++;  
                    }
                    
                    foreach($blobResponse as $blobStatus){
                        if($blobStatus['success'] == TRUE || $blobStatus["success"] == 1|| $blobStatus["success"] == 'true'){
                            $destinationURL[$count]['destinationURL'] =  $blobStatus['destinationURL'];
                            $destinationURL[$count]['mimeType'] =  $blobStatus['mimeType'];
                            $count++;
                        }  
                    }
                    
                    if(count($destinationURL) > 0){
                        $response = attachment::SaveAttachmentsForWeb($model_master->id, $model_master->creator_id, $destinationURL);

                    if ($response["success"] == 1 || $response["success"] == "true" || $response["success"] == 'true') {
                            
                            notification::notifyNewWithAttachment($model_master->id, $model_master->newsflash_allowed, false, $reptoffeder, $destinationURL);
                            Yii::$app->session->setFlash('success', "Attachment has been added successfully");
    
                        }
                    }else{
                            Yii::$app->session->setFlash('success', "Attachment already exists");
                            return $this->render('update', [
                                'model_master' => $model_master,
                                'model_detail' => $model_detail,
                                'followerUserlist' => $followerUserlist,
                        ]);
                    }
                } else {
                    notification::notifyNew($model_master->id, $model_master->newsflash_allowed, false, $reptoffeder);
                }
                }
             
                return $this->redirect('index');
            } else {
                return $this->render('update', [
                    'model_master' => $model_master,
                    'model_detail' => $model_detail,
                    'followerUserlist' => $followerUserlist,
                ]);
            }
        } else {
            // Dated
            if (APP_CASE_INCIDENT == $model_master->app_case_type_id) {
                $model_detail->incident_date = $this->dateFormat($model_detail->incident_datetime);
                $model_detail->incident_time = $this->timeFormat($model_detail->incident_datetime);
            } else {
                $model_detail->correction_date = $this->dateFormat($model_detail->correction_date);
            }
            return $this->render('update', [
                'model_master' => $model_master,
                'model_detail' => $model_detail,
                'followerUserlist' => $followerUserlist,
            ]);
        }
    }

    /**
     * Deletes an existing AppCase model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Issue ID
     *
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AppCase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Issue ID
     *
     * @return Loaded AppCase model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (( $model = AppCase::findOne($id) ) !== NULL) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Date Format
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function dateFormat($value) {
        return date_format(date_create($value ?? ""), "M d, Y");
    }

    /**
     * Time Format
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function timeFormat($value) {
        return date_format(date_create($value), "G i A");
    }

    /**
     * Date Format To SQL
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function dateFormatToSQL($value) {
        return date_format(date_create($value), "Y-m-d H:i:s");
    }

    public static function getFollowersList($userId)
    {
        $sqlQuery = (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ? "SELECT distinct u.email FROM [user] u where u.email != '' and u.is_active = 1 group by u.id,u.email" : "SELECT distinct u.email FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id left join [dbo].[user_jobsite] J on UJ.jobsite_id = J.jobsite_id where j.user_id = ".$userId." and u.email != '' and u.is_active = 1 group by u.id,u.email";
     

       $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();

        $userListdataArray = array();
        foreach ($usersList as $key => $value) { 
          $userListdataArray[$value['email']] = $value['email'];
      }

      return $userListdataArray;
    }
}
