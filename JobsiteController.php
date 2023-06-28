<?php

namespace app\controllers;

use Yii;
use app\models\Jobsite;
use app\models\UserJobsite;
use app\models\User;
use app\models\Contractor;
use app\models\searches\Jobsite as JobsiteSearch;
use app\models\searches\User as UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
        $model = new Jobsite();

		// set default active
		$model->is_active = 1;

       // set default Newsflash(Safety Alert) true
        $model->newsflash_allowed = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
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

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'jobsite_id = '. $id, $displayinactiveusers );
        $dataProvider->pagination->pageSize=7;
        $dataContractor_sp=Yii::$app->db->createCommand("exec [dbo].[DataContractors] '".$id."'")->queryAll();
        $dataContractor = ArrayHelper::map($dataContractor_sp, 'id', 'contractor');

        // User Post Data
        $postData = Yii::$app->request->post();

        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
            //Yii::$app->request->post("User")["jobsites"]
            // User
            $trasanction = $model->getDb()->beginTransaction();
            //$model->id = $id;
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
        $model = new User();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'dataContractor' => $dataContractor,
            'searchModel' => $searchModel,
            'userModel' => $model
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
