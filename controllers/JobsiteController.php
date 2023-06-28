<?php

namespace app\controllers;

use Yii;
use app\models\Jobsite;
use app\models\UserJobsite;
use app\models\User;
use app\models\Contractor;
use app\models\ContractorJobsite;
use app\models\searches\Jobsite as JobsiteSearch;
use app\models\searches\User as UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\notification;

/**
 * JobsiteController implements the CRUD actions for Jobsite model.
 */
class JobsiteController extends AllController
{
    public function behaviors()
    {
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
    public function beforeAction( $action )
    {
        if (Yii::$app->session->get( "user.role_id" ) != ROLE_SYSTEM_ADMIN)
        {
            return $this->redirect(['\index']);
        }

        if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_TRADE_PARTNER)
        {
            return $this->redirect( ['/dashboard'] );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all Jobsite models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new JobsiteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Jobsite model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $model = new Jobsite();
        
        // set cmic flag
        $model->is_cmic = 1;

        if(isset($postData["Jobsite"])){
            
            $model->is_active = $postData["Jobsite"]["is_active"];
            $model->photo_allowed = $postData["Jobsite"]["photo_allowed"];
            $model->newsflash_allowed = $postData["Jobsite"]["newsflash_allowed"];
        }
		

        if ($model->load($postData) && $model->save()) {

            $job_number = $model->job_number;
            $jobsite_id = $model->id;
            $job_ad1 = $postData["jobAdm1"];
            $job_ad2 = $postData["jobAdm2"];
            $jobsite_admins = array();

            $contractor_jobsite = new ContractorJobsite();
            $contractor_jobsite->contractor_id = 148;
            $contractor_jobsite->jobsite_id = $jobsite_id;
            $contractor_jobsite->save();

            array_push($jobsite_admins, $job_ad1, $job_ad2);
            
            for($i=0; $i < count( $jobsite_admins ); $i++)
            {
                if(!empty($jobsite_admins[$i])){
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->jobsite_id = $jobsite_id;
                    $user_jobsite_model->user_id = (int)$jobsite_admins[$i];
                    $user_jobsite_model->is_admin = 1;

                    if( !$user_jobsite_model->save() )
                    {
                        $user_jobsite_status = false;

                    }else{
                        $sqlQuery = "update [dbo].[user] set role_id = 1 where id=".(int)$jobsite_admins[$i];  
                        $update_user_role_id = Yii::$app->db->createCommand("$sqlQuery")->execute();
                    }
                }
                    
                }
                notification::notifyNewJobsite($job_number,$jobsite_id);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Jobsite model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post();

        if(isset($postData["Jobsite"])){
            
            $model->is_active = $postData["Jobsite"]["is_active"];
            $model->photo_allowed = $postData["Jobsite"]["photo_allowed"];
            $model->newsflash_allowed = $postData["Jobsite"]["newsflash_allowed"];

        }

        if ($model->load($postData) && $model->save()) {

            $job_ad1 = $postData["jobAdm1"];
            $job_ad2 = $postData["jobAdm2"];

            $sqlQuery = "update [dbo].[user_jobsite]  set is_admin = 0 where jobsite_id = $model->id ";  
            $update_user_is_admin = Yii::$app->db->createCommand("$sqlQuery")->execute();

            $jobsite_admins = array();
            $pre_jobsite_admins = array();
            array_push($jobsite_admins, $job_ad1, $job_ad2);
            $nw_job_admins = array_values(array_filter($jobsite_admins));
            $job_admins = UserJobsite::find()->select('user_id')->joinWith('user')->where(["jobsite_id" => $id])->asArray()->all();
            
            for($i=0; count( $job_admins ) > $i; $i++){
                if(isset($job_admins[$i]["user_id"])){
                    array_push($pre_jobsite_admins, $job_admins[$i]["user_id"]);
                }
            }

            for($i=0; $i < count($nw_job_admins); $i++)
                {
                    if(in_array($nw_job_admins[$i], $pre_jobsite_admins)){
                        $sqlQuery = "update [dbo].[user_jobsite]  set is_admin = 1 where jobsite_id = $model->id AND user_id=".(int)$nw_job_admins[$i];  
                        $update_user_is_admin = Yii::$app->db->createCommand("$sqlQuery")->execute();
                        if($update_user_is_admin){
                            $sqlQuery = "update [dbo].[user]  set role_id = 1 where id=".(int)$nw_job_admins[$i];  
                            $update_user_role_id = Yii::$app->db->createCommand("$sqlQuery")->execute();
                        }
                    }else{
                        if(isset($nw_job_admins[$i])){
                            $user_jobsite_model = new UserJobsite();
                            $user_jobsite_model->user_id = (int)$nw_job_admins[$i];
                            $user_jobsite_model->jobsite_id = $model->id;
                            $user_jobsite_model->is_admin = 1;
    
                            if( !$user_jobsite_model->save() )
                            {
                                $user_jobsite_status = false;
                                
                            }else{
                                $sqlQuery = "update [dbo].[user]  set role_id = 1 where id=".(int)$nw_job_admins[$i];  
                                $update_user_role_id = Yii::$app->db->createCommand("$sqlQuery")->execute();
                            }
                        }
                    }
                    
                }

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    public function actionView($id)
    {
        $searchModel = new UserSearch();

        $model = new User();

        $displayinactiveusers = false;
        if( Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN)
        {
             $displayinactiveusers = true;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'jobsite_id = '. $id, $displayinactiveusers,NULL,true);
        $dataProvider->pagination->pageSize=7;
        $dataContractor_sp=Yii::$app->db->createCommand("exec [dbo].[DataContractors] '".$id."'")->queryAll();
        $dataContractor = ArrayHelper::map($dataContractor_sp, 'id', 'contractor');

        // User Post Data
        $postData = Yii::$app->request->post();

        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
            $trasanction = $model->getDb()->beginTransaction();
            
            $model->created = date("Y-m-d H:i:s");
            $model->updated = date("Y-m-d H:i:s");
            $model_status = $model->save();

            // User-Jobsite Save
            $user_jobsite_status = true;
            $user_jobsite_model = new UserJobsite();
            $user_jobsite_model->user_id = $model->id;
            $user_jobsite_model->jobsite_id = $id;
            if( !$user_jobsite_model->save() ) {
                $user_jobsite_status = false;
            }

            if($model_status && $user_jobsite_status) {
                $trasanction->commit();
            }

        }

        $curr_job_admins = UserJobsite::find()->select('first_name, last_name')->joinWith('user')->where(["jobsite_id" => $id, "is_admin"=>1])->asArray()->all();
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'dataContractor' => $dataContractor,
            'searchModel' => $searchModel,
            'userModel' => $model,
            'jobAdmins' => $curr_job_admins
        ]);
    }

    /**
     * Deletes an existing Jobsite model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Jobsite model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Jobsite the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Jobsite::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
