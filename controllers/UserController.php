<?php

namespace app\controllers;

use app\components\sqlRoleBuilder;
use app\helpers\functions;
use app\models\ChangesTracker;
use app\models\LoginTracker;
use app\models\Contractor;
use Yii;
use app\models\User;
use app\models\Jobsite;
use app\models\UserJobsite;
use app\models\Role;
use app\models\AppCase;
use app\models\AppCaseViolation;
use app\models\AppCaseObservation;
use app\models\AppCaseRecognition;
use app\models\AppCaseHistory;
use app\models\Session;
use app\models\Device;
use app\models\Content;
use app\models\Comment;
use app\models\Follower;
use app\models\FormChangePass;
use app\models\MergeUsers;
use app\models\searches\User as UserSearch;
use app\components\notification;
use yii\debug\models\search\Log;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AllController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Reroutes based on roles and permissions.
     * @return mixed
     */
     public function beforeAction( $action )
    {
       if($action->id == "profile" || $action->id == "password"){
            return parent::beforeAction( $action );
        }else if( Yii::$app->session->get('user.role_id') != ROLE_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_TRADE_PARTNER)
        {
 
            return $this->redirect( array( 'app-case/index' ) );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->session->get( 'user.role_id' ) == ROLE_TRADE_PARTNER)
        {
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
            if($filterByJobsite)
            {
              $response =' and ';
              $loggedInUser  = Yii::$app->session->get( "user.id" );
              $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
              $filterByJobsite = $filterByJobsite.$response.'contractor_id ='.$contractorID['contractor_id'] ;
            }
            else
            {
            $loggedInUser  = Yii::$app->session->get( "user.id" );
              $contractorID = User::find()->select('contractor_id')->where(['id' => $loggedInUser])->one();
              $filterByJobsite = 'contractor_id ='.$contractorID['contractor_id'];
            }
        } 
       elseif ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( 'user.role_id' ) != ROLE_WT_EXECUTIVE_MANAGER
         )
        {
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        }
        else
        {
            $filterByJobsite = '';
        }

         $displayinactiveusers = false;
        if( Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN)
        {
             $displayinactiveusers = true;
        }
        
        $GetJobsiteContractors = null;
        if(Yii::$app->session->get('user.role_id') == ROLE_ADMIN )
        {
        $GetUserJobsites = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        $GetJobsiteContractors = sqlRoleBuilder::getContractorsByJobsiteIdinusersearch($GetUserJobsites);
        }

        
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterByJobsite,$displayinactiveusers,$GetJobsiteContractors);
        $loggedInUser  = Yii::$app->session->get( "user.id" );

        if(isset($loggedInUser)){
          if( Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN){
            $data_query = Yii::$app->db->createCommand("exec [dbo].[UserJobsites]")->queryAll();
            }else {
              $query = "SELECT j.id, j.jobsite
              FROM [dbo].[user_jobsite] uj
              inner join [dbo].[jobsite] j on j.id = uj.jobsite_id
              WHERE uj.user_id=".$loggedInUser."";
              $data_query = Yii::$app->db->createCommand($query)->queryAll();
            }
        }else {
          return $this->redirect( array( 'home/index' ) );
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'data_query' => $data_query
        ]);
    }

    /**
     * Displays a single user model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $activities = LoginTracker::find()->where("user_id = $id")->orderBy("timestamp DESC")->asArray()->all();
        $changes = ChangesTracker::find()->where(['model_name' => 'User', 'model_id' => $id])->orderBy("timestamp DESC")->asArray()->all();
        $changesArray = array();
        foreach($changes as $change){
            $userData = User::find()->where(['id' => $change["user_id"]])->asArray()->one();
            $change["userData"] = $userData;
            $changesArray[] = $change;
        }
        $changes = $changesArray;
        return $this->render('view', [
            'model' => $this->findModel($id),
        'activities' => $activities,
            'changes' => $changes
        ]);
    }

	/**
     * Displays a single user model.
     * @param integer $id
     * @return mixed
     */
    public function actionProfile()
    {
  		if( !Yii::$app->session->get('user.id') ){
  			return $this->redirect(Yii::getAlias('@web'));
  		}

  		return $this->render('profile', [
              'model' => $this->findModel( Yii::$app->session->get('user.id') )
          ]);
    }

	/**
     * Displays a single user model.
     * @param integer $id
     * @return mixed
     */
    public function actionPassword()
    {
  		if( !Yii::$app->session->get('user.id') ){
  			return $this->redirect(Yii::getAlias('@web'));
  		}

  		$model = new FormChangePass();

  		if ($model->load(Yii::$app->request->post())){
  			if ($model->validate()){

  				$user_table = $this->findModel( Yii::$app->session->get('user.id') );
  				$user_table->password = md5( $model->password );

  				if ($user_table->save()){
                      notification::notifyRecovery(Yii::$app->session->get('user.id'), $model->password);
  					return $this->redirect(["login/logout"]);
  				}

  			}else{
  				$model->getErrors();
  			}
  		}

  		return $this->render('password', [
              'model' => $this->findModel( Yii::$app->session->get('user.id') ),
              'model_form' => $model
          ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        
        // set default active
        $model->is_active = 1;
        $model->IsAduser  = 0;
        $password = "";

        // User Post Data
        $postData = Yii::$app->request->post();
        if(isset($postData["User"]))
            $postData["User"]["phone"] = str_replace("-","",$postData["User"]["phone"]);

         if(isset($postData["wtuser-rb"])){
            $IsADuser = $postData["wtuser-rb"];
           
             $model->IsAduser =  ($IsADuser == "0")? 0 : 1; 
         }
        //var_dump($postData);
        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
            //Evitar repost en IE.
            //Leer si existe ya el registro que se quiere grabar.
          $tz_nonaccess_users = array(10,11,12,13,14);
          $tz_onlyusernameaccess_users = array(7,8,15,16,18);
          $tz_fullaccess_users = array(1,2,3,4,5,6);

          if(isset($model->role_id) &&  in_array($model->role_id, $tz_fullaccess_users))
          {
            $sql = "SELECT * from [user] WHERE ";
            if(isset($model->contractor_id)){
              $sql.= "[user].contractor_id = $model->contractor_id ";
            }
            if(isset($model->role_id)){
              $sql.= "AND [user].role_id = $model->role_id ";
            }
            if(isset($model->first_name)){
              $fs = str_replace("'", "''",$model->first_name);
              $sql.= "AND [user].first_name = '$fs' ";
            }
            if(isset($model->last_name)){
              $ls = str_replace("'", "''",$model->last_name);
              $sql.= "AND [user].last_name = '$ls' ";
            }
            if(isset($model->user_name)){
              $user_name = str_replace("'", "''",$model->user_name);
              $sql.= "AND [user].user_name = '$user_name' ";
            }
            if(isset($model->email)){
              $email = str_replace("'", "''",$model->email);
              $sql.= "AND [user].email = '$email' ";             
            }
            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();
            if($recordAlreadyExists){
              Yii::$app->session->setFlash('error','Alert: Username already exist. Provide a different username.');
              return $this->redirect(['user/index', 'id' => $model->id]);
            }

          }else if(isset($model->role_id) &&  in_array($model->role_id, $tz_onlyusernameaccess_users))
          {

           $sql = "SELECT * from [user] WHERE ";

            if(isset($model->user_name)){
              $user_name = str_replace("'", "''",$model->user_name);
              $sql.= "[user].user_name = '$user_name' ";
            }

            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();
            if($recordAlreadyExists){

              Yii::$app->session->setFlash('error','Alert: User already exist!');
              return $this->redirect(['user/index', 'id' => $model->id]);
            }

          } else if(isset($model->role_id) &&  in_array($model->role_id, $tz_nonaccess_users))
          {

           $sql = "SELECT * from [user] left join [dbo].[user_jobsite] on [user_jobsite].user_id = [user].id WHERE "; 

           if(isset($model->contractor_id)){
              $sql.= "[user].contractor_id = $model->contractor_id ";
            }
            if(isset($model->role_id)){
              $sql.= "AND [user].role_id = $model->role_id ";
            }
            if(isset($model->first_name)){
              $fs = str_replace("'", "''",$model->first_name);
              $sql.= "AND [user].first_name = '$fs' ";
            }
            if(isset($model->last_name)){
              $ls = str_replace("'", "''",$model->last_name);
              $sql.= "AND [user].last_name = '$ls' ";
            }

            if(isset(Yii::$app->request->post("User")["jobsites"])){
              $jobsites_array = Yii::$app->request->post("User")["jobsites"];
              $jobsites_data = implode(',', $jobsites_array);
              $sql.= "AND [user_jobsite].jobsite_id in ($jobsites_data) ";
            }        

            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();                
            if($recordAlreadyExists){ 
              Yii::$app->session->setFlash('error','Alert: User already exist!');
              return $this->redirect(['user/index', 'id' => $model->id]);
            }

          }

            //////////////////////////////////////////////////////////////////////////////////////////////
            /////////// descomentar para generar password automaticamente si no ingresan uno  ////////////
            //////////////////////////////////////////////////////////////////////////////////////////////
            if( $model->IsAduser != 1){
              $password = $model->password;
              $model->password = md5($model->password);
            }
            

            //            $password = "";
//            if(!empty($model->user_name) && empty($model->password)){
//                //generate random password
//                $rand_pass = uniqid();
//                //set password
//                $password = $rand_pass;
//                $model->password = md5( $rand_pass );
//            }else{
//                $password = $model->password;
//                $model->password = md5($model->password);
//            }


            // User
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();
          
            // User-Jobsite Save
            $user_jobsite_status = true;
            if( isset( Yii::$app->request->post("User")["jobsites"] ) )
            {
                $jobsites = Yii::$app->request->post("User")["jobsites"];

                for($i=0; $i < count($jobsites); $i++)
                {
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->user_id = $model->id;
                    $user_jobsite_model->jobsite_id = $jobsites[$i];
                    //$user_jobsite_model->is_admin = 0;
                    if( !$user_jobsite_model->save() )
                    {
                        $user_jobsite_status = false;
                        break;
                    }
                }
            }

            if($model_status && $user_jobsite_status)
            {
            	
                $transaction->commit();
        
              if (in_array($model->role_id, $tz_fullaccess_users) || in_array($model->role_id, $tz_nonaccess_users)|| in_array($model->role_id, $tz_onlyusernameaccess_users))
              {
                notification::notifyNewUser($model->id , $password);
              }
                
              Yii::$app->session->setFlash('success','Alert: User Creation successful!');
              
              return $this->redirect(['user/index', 'id' => $model->id]);
                
            }
            else
            {
            	
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
        	
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (($model->role_id == ROLE_SYSTEM_ADMIN)  && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN)) {
          return $this->redirect(['index', 'id' => $model->id]);
        }
        // User Post Data
        $postData = Yii::$app->request->post();

        if(count($postData) > 0){
          $originalModel = $this->findModel($id);
          $old_pasword = $model->password;
        }
        
        if(isset($postData["User"]))
            $postData["User"]["phone"] = str_replace("-","",$postData["User"]["phone"]);
        if(isset($postData["User"]["emergency_contact"]))
            $postData["User"]["emergency_contact"] = (int)str_replace("-","",$postData["User"]["emergency_contact"]);  


        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
            //check for changes on password
            $password = '';
            if ( $old_pasword != $model->password )
            {
                $password = $model->password;
                $model->password = md5( $model->password );
            }

            // User
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();

            //check for changes in badge number or company/contractor
            $logged_user_id = Yii::$app->session->get( "user.id" );
            $originalModel->employee_number == $model->employee_number ? null : functions::trackChange($logged_user_id, $id, 'User', 'Employee number', $originalModel->employee_number, $model->employee_number);
            $originalModel->contractor == $model->contractor ? null : functions::trackChange($logged_user_id, $id, 'User', 'Contractor', $originalModel->contractor->contractor, $model->contractor->contractor);

            // User-Jobsite Save
            $user_jobsite_status = true;
            $jobsites_selected = [];

            $recv_disabledjobsite = Yii::$app->request->post("disabledjobsites");
            if( isset( Yii::$app->request->post("User")["jobsites"]) || isset($recv_disabledjobsite) )
            {
              
              if( isset( Yii::$app->request->post("User")["jobsites"]))
                 $jobsites_selected = Yii::$app->request->post("User")["jobsites"];

              $disabledarray = array_filter(explode(",",Yii::$app->request->post("disabledjobsites")));
              $jobsites = array_merge($jobsites_selected,$disabledarray);

                // Delete All Old
                UserJobsite::deleteAll( ["user_id"=>$id] );

                for($i=0; $i < count($jobsites); $i++)
                {
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->user_id = $model->id;
                    $user_jobsite_model->jobsite_id = $jobsites[$i];
                    if( !$user_jobsite_model->save() )
                    {
                        $user_jobsite_status = false;
                        break;
                    }
                }
            }else{
                UserJobsite::deleteAll( ["user_id"=>$id] );
            }

            if($model_status && $user_jobsite_status)
            {
                $transaction->commit();
                if($password != ""){
                    notification::notifyChangeUser($model->id , $password);
                }

                return $this->redirect(['view?id='. $model->id]);
            }
            else
            {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else 
           {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

   /**
     * Updates an existing WT CraftMen User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatecraftmen($id)
    {
        $model = $this->findModel($id);
        $originalModel = $this->findModel($id);
        
        if (($model->role_id == ROLE_SYSTEM_ADMIN)  && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN)) {
          return $this->redirect(['index', 'id' => $model->id]);
        }
        // User Post Data
        $postData = Yii::$app->request->post();
        if(isset($postData["User"]))
            $postData["User"]["phone"] = str_replace("-","",$postData["User"]["phone"]);
        if(isset($postData["User"]["emergency_contact"]))
            $postData["User"]["emergency_contact"] = (int)str_replace("-","",$postData["User"]["emergency_contact"]);  

      
        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
           
            // User
            $model->role_id = 19;
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();

            //check for changes in badge number or company/contractor
            $logged_user_id = Yii::$app->session->get( "user.id" );
            $originalModel->employee_number == $model->employee_number ? null : functions::trackChange($logged_user_id, $id, 'User', 'Employee number', $originalModel->employee_number, $model->employee_number);
            $originalModel->contractor == $model->contractor ? null : functions::trackChange($logged_user_id, $id, 'User', 'Contractor', $originalModel->contractor->contractor, $model->contractor->contractor);

            // User-Jobsite Save
            $user_jobsite_status = true;
            $jobsites_selected = [];

            $recv_disabledjobsite = Yii::$app->request->post("disabledjobsites");
            if( isset( Yii::$app->request->post("User")["jobsites"]) || isset($recv_disabledjobsite) )
            {
              
              if( isset( Yii::$app->request->post("User")["jobsites"]))
                 $jobsites_selected = Yii::$app->request->post("User")["jobsites"];

              $disabledarray = array_filter(explode(",",Yii::$app->request->post("disabledjobsites")));
              $jobsites = array_merge($jobsites_selected,$disabledarray);

                // Delete All Old
                UserJobsite::deleteAll( ["user_id"=>$id] );

                for($i=0; $i < count($jobsites); $i++)
                {
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->user_id = $model->id;
                    $user_jobsite_model->jobsite_id = $jobsites[$i];
                    if( !$user_jobsite_model->save() )
                    {
                        $user_jobsite_status = false;
                        break;
                    }
                }
            }else{
                UserJobsite::deleteAll( ["user_id"=>$id] );
            }


            if($model_status && $user_jobsite_status)
            {
                $transaction->commit();

                return $this->redirect(['view?id='. $model->id]);
            }
            else
            {
             // echo "step11"; exit();
                return $this->render('updatecraftmen', [
                    'model' => $model,
                ]);
            }
        } else {
          //echo "step21"; exit();
            return $this->render('updatecraftmen', [
                'model' => $model,
            ]);
        }
    }

     /**
     * Updates an existing user model only username only for special user permissions.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionSplupdate($id)
    {
        
        $model = $this->findModel($id);
        $originalModel = $this->findModel($id);
       
        if (($model->role_id == ROLE_SYSTEM_ADMIN)  && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN)) {
          return $this->redirect(['index', 'id' => $model->id]);
        }
        // User Post Data
        $postData = Yii::$app->request->post();
//var_dump($postData); exit();

        if ($postData)
        {        
       
      $sql = "Select 1  From [dbo].[user] Where user_name='".$postData['User']['user_name']."' and IsAduser = ".$postData['wtuser-rb'];      
        
       
      $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll(); 

      if($postData['User']['user_name'] == "")
      	 $recordAlreadyExists = false;

      if($recordAlreadyExists){
         Yii::$app->session->setFlash('error','Alert: UserName Already Exist!');
        return $this->render('useredit', [
                'model' => $model,
            ]);
      }  else{

      $sqlQuery = "update [dbo].[user]  set user_name = '".$postData['User']['user_name']."', IsAduser = ".$postData["wtuser-rb"].",is_active = ".$postData['User']['is_active']."  where id=".(int)$id;  

        $data_creator = Yii::$app->db->createCommand("$sqlQuery")->execute();
        Yii::$app->session->setFlash('success','Alert: User updated successful!');
              
        return $this->redirect(['view?id='. $model->id]);

      } 
       




        } else {
            return $this->render('useredit', [
                'model' => $model,
            ]);
        }
    }

     /**
     * Provide an option Merge the Inactive user Issues to Active user Issues.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionMergeUser()
    {
      $id = Yii::$app->session->get('user.id');
        $model = $this->findModel($id);
        
        // User Post Data
        $postData = Yii::$app->request->post();
         //var_dump($postData); exit();

       if ($postData)
        {
           $sql = "Select 1  From [dbo].[merge_users] Where parent_userid='".$postData["parent-user"]."' and child_userid = ".$postData['mergeuser'];      
        
       
            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll(); 
      if($recordAlreadyExists){
               Yii::$app->session->setFlash('error','Alert: UserMap already exist!');
               return $this->render('usermerge', [
                          'model' => $model,
                      ]);
            }  else{
          $mergeuser = new MergeUsers();
          $mergeuser->parent_userid = (int)$postData["parent-user"];
          $mergeuser->child_userid = (int)$postData["mergeuser"];
          $mergeuser_status = $mergeuser->save();


            return $this->render('usermerge', [
                          'model' => $model,
                      ]);
           }
        } else {
            return $this->render('usermerge', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionDelete($id)
    {
      $model = $this->findModel($id);
      $errors = [];
      $activities = [];
      $changes = [];
      if(Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN){
        $authorized = true;
        // echo "User: $id<br>";
        $appCaseData = AppCase::find()->where(['creator_id' => $id])->asArray()->one();
        // var_dump($appCaseData);
        if($appCaseData != null){
          $errors[] = array("message" => "Appears as creator in issue ", "path" => "/app-case/view?id=", "id" => $appCaseData['id']);
        };
        $appCaseData = AppCase::find()->where(['affected_user_id' => $id])->asArray()->one();
        if($appCaseData != null){
          $errors[] = array("message" => "Appears as affected user in issue ", "path" => "/app-case/view?id=", "id" => $appCaseData['id']);
        };
        $appCaseViolationData = AppCaseViolation::find()->where(['foreman_id' => $id])->asArray()->one();
        if($appCaseViolationData != null){
          $errors[] = array("message" => "Appears as foreman in violation issue ", "path" => "/app-case/view?id=", "id" => $appCaseViolationData['app_case_id']);
        };
        $appCaseObservationData = AppCaseObservation::find()->where(['foreman_id' => $id])->asArray()->one();
        if($appCaseObservationData != null){
          $errors[] = array("message" => "Appears as foreman in observation issue ", "path" => "/app-case/view?id=", "id" => $appCaseObservationData['app_case_id']);
        };
        $appCaseRecognitionData = AppCaseRecognition::find()->where(['foreman_id' => $id])->asArray()->one();
        if($appCaseRecognitionData != null){
          $errors[] = array("message" => "Appears as foreman in recognition issue ", "path" => "/app-case/view?id=", "id" => $appCaseRecognitionData['app_case_id']);
        };
        $commentData = Comment::find()->where(['user_id' => $id])->asArray()->one();
        if($commentData != null){
          $errors[] = array("message" => "Has posted a comment in issue ", "path" => "/app-case/view?id=", "id" => $commentData['app_case_id']);
        };
        $activities = LoginTracker::find()->where("user_id = $id")->orderBy("timestamp DESC")->asArray()->all();
        $changes = ChangesTracker::find()->where(['model_name' => 'User', 'model_id' => $id])->orderBy("timestamp DESC")->asArray()->all();
        $changesArray = array();
        foreach($changes as $change){
            $userData = User::find()->where(['id' => $change["user_id"]])->asArray()->one();
            $change["userData"] = $userData;
            $changesArray[] = $change;
        }
        $changes = $changesArray;
      } else {
        $authorized = false;
      }
      return $this->render('delete', [
          'model' => $model,
          'authorized' => $authorized,
          'errors' => $errors,
          'activities' => $activities,
          'changes' => $changes
      ]);
    }

    /**
     * Generate QR Code
     */

    public function actionGenQrCode()
    {     

      return $this->render('genqrcode');

}
	  public function actionUsersTemplate()
    {
      $jobsites = Yii::$app->request->post('jobsites');
     
      if(isset($jobsites)){
        $filterByJobsite = 'jobsite_id IN (' . implode( ',', $jobsites ) . ')';
      $row = 2; 
      $query = "with cte_users (
        id, is_active, first_name, last_name, employee_number, sop
        ,created
        ,role
        ,contractor
        ,jobsite_id, jobsite
      ) AS (
          SELECT u.id,u.is_active,u.first_name,u.last_name,u.employee_number,u.sop,u.created,r.role,c.contractor,j.id, j.jobsite
          FROM [dbo].[user_jobsite] uj
          inner join [dbo].[user] u on u.id = uj.user_id
          inner join [dbo].[role] r on r.id = u.role_id
          inner join [dbo].[contractor] c on c.id = u.contractor_id
          inner join [dbo].[jobsite] j on j.id = uj.jobsite_id
          WHERE u.is_active = 1 AND uj.".$filterByJobsite."
      ),
      cte_app_case(user_id,jobsite_id,violation,recognition,incident,observation)AS(SELECT uj.user_id,uj.jobsite_id
            ,count(case when ac.app_case_type_id='1'  then 1 else null end) 
            ,count(case when ac.app_case_type_id='2'  then 1 else null end) 
            ,count(case when ac.app_case_type_id='3'  then 1 else null end) 
            ,count(case when ac.app_case_type_id='4'  then 1 else null end) 
            FROM [dbo].[user_jobsite] uj
            inner join [dbo].[app_case] ac on ac.affected_user_id = uj.user_id AND ac.jobsite_id = uj.jobsite_id
            WHERE uj.".$filterByJobsite."
            GROUP BY uj.user_id,uj.jobsite_id,ac.affected_user_id
      ),
      cte_created_issues(createdissues,user_id,jobsite_id)AS(
            SELECT 
            count(case when ac.creator_id = uj.user_id AND ac.app_case_type_id='4' OR ac.app_case_type_id='3' OR ac.app_case_type_id='2' OR ac.app_case_type_id='1'  then 1 else null end)
            ,uj.user_id,uj.jobsite_id
            FROM [dbo].[user_jobsite] uj
            inner join [dbo].[app_case] ac on ac.creator_id = uj.user_id AND ac.jobsite_id = uj.jobsite_id
            WHERE uj.".$filterByJobsite."
            GROUP BY uj.user_id,uj.jobsite_id
      )
            SELECT 
            cu.id, cu.is_active, TRIM('* ' FROM cu.first_name) as first_name, TRIM('* ' FROM cu.last_name) as last_name,  TRIM(' ' FROM cu.employee_number) as employee_number, cu.sop
            ,cu.created
            ,cu.role
            ,cu.contractor
            ,coalesce(ca.violation, 0) as violation
            ,coalesce(ca.recognition, 0) as recognition
            ,coalesce(ca.incident, 0) as incident
            ,coalesce(ca.observation, 0) as observation
            ,cu.jobsite
            ,coalesce(cci.createdissues, 0) as createdIssues
            FROM cte_users cu
            left join cte_app_case ca on ca.user_id = cu.id AND ca.jobsite_id = cu.jobsite_id
            left join cte_created_issues cci on cci.user_id = cu.id AND cci.jobsite_id = cu.jobsite_id
            ORDER BY cu.created DESC, first_name ASC,last_name ASC;";
      
      $data_query = Yii::$app->db->createCommand($query)->queryAll();

      //$excel = \PHPExcel_IOFactory::createReader("Excel2007");
      $excel = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
      $excel = $excel->load("../excel_templates/UsersListTemplate.xlsx");
      $excel->getActiveSheet(0)->setTitle('Users')
                               ->setCellValue('A1', 'Status')
                               ->setCellValue('B1', 'First Name')
                               ->setCellValue('C1', 'Last Name')
                               ->setCellValue('D1', 'Emp. ID')
                               ->setCellValue('E1', 'Role')
                               ->setCellValue('F1', 'Contractor')
                               ->setCellValue('G1', 'Created')
                               ->setCellValue('H1', 'SOP')
                               ->setCellValue('I1', 'Recognition')
                               ->setCellValue('J1', 'Violation')
                               ->setCellValue('K1', 'Observation')
                               ->setCellValue('L1', 'Incident')
                               ->setCellValue('M1', 'Jobsite')
                               ->setCellValue('N1', 'Total number of issues created by user');
      
      foreach($data_query as $user_data){
            
            if($user_data['is_active'] == 1){
              $status = 'Active';
            }

            if($user_data['sop'] == 1){
              $sop = 'Yes';
            }else if($user_data['sop'] == 0){
              $sop = 'No';
            }

            $firstname = $user_data['first_name'];
            $lastname = $user_data['last_name'];
            $created = date("m-d-Y", strtotime($user_data['created']));
            $role =  $user_data['role'];
            $emp_num =  $user_data['employee_number'];      
            $contractor = $user_data['contractor'];
            $jobsite = $user_data['jobsite'];
            $recognition = $user_data['recognition'];
            $voilation = $user_data['violation'];
            $incident = $user_data['incident'];
            $observation = $user_data['observation'];
            $createdIssues = $user_data['createdIssues'];

            
            if($user_data['recognition'] == NULL){
              $recognition = 0;
            }
            if($user_data['violation'] == NULL){
              $voilation = 0;
            }
            if($user_data['incident'] == NULL){
              $incident = 0;
            }
            if($user_data['observation'] == NULL){
              $observation = 0;
            }
            
            
            $excel->getActiveSheet()
                ->setCellValue("A$row", $status)
                ->setCellValue("B$row", $firstname)
                ->setCellValue("C$row", $lastname)
                ->setCellValue("D$row", $emp_num)
                ->setCellValue("E$row", $role)
                ->setCellValue("F$row", $contractor)
                ->setCellValue("G$row", $created)
                ->setCellValue("H$row", $sop)
                ->setCellValue("I$row", $recognition)
                ->setCellValue("J$row", $voilation)
                ->setCellValue("K$row", $observation)
                ->setCellValue("L$row", $incident)
                ->setCellValue("M$row", $jobsite)
                ->setCellValue("N$row", $createdIssues);
            $row++;
      }
      
      $excel->setActiveSheetIndex(0);
        //$objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="UsersListTemplate.xlsx"');
        // for ($i = 0; $i < ob_get_level(); $i++) {
        //   ob_end_flush();
        // }
        // ob_implicit_flush(1);
        //ob_clean();
        $objWriter->save('../temp/UsersListTemplate.xlsx');
        header('Content-type: application/vnd.ms-excel');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        header('Content-Disposition: attachment; filename="UsersListTemplate.xlsx"');
        header('Content-Length: ' . filesize("../temp/UsersListTemplate.xlsx"));
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        //ob_end_clean();
        $objWriter->save('php://output');
        exit();
        
      }else{
        Yii::$app->session->setFlash('jobsite','Please choose atleast one jobsite!');
        return $this->redirect('index');
      }  
      

      
        
    }

    public function actionJobsitesList()
    {
      $loggedInUser  = Yii::$app->session->get( "user.id" );
      $query = "SELECT j.id, j.jobsite
      FROM [dbo].[user_jobsite] uj
      inner join [dbo].[jobsite] j on j.id = uj.jobsite_id
      WHERE uj.user_id=".$loggedInUser."";
      
      $data_query = Yii::$app->db->createCommand($query)->queryAll();
      
      
            
      return $this->render('jobsites', [
        'data_query' => $data_query
      ]);
            
    }

     

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
