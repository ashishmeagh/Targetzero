<?php

namespace app\controllers;

use Yii;
use app\models\SubJobsite;
use app\models\searches\SubJobsite as SubJobsiteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
 use app\components\sqlRoleBuilder;
/**
 * SubJobsiteController implements the CRUD actions for SubJobsite model.
 */
class SubJobsiteController extends Controller
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
        if($action->id == "update" || $action->id == "create"){
            if (Yii::$app->session->get( "user.role_id" ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( "user.role_id" ) != ROLE_ADMIN)
            {
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_SAFETY_PERSONNEL )
        {
            return $this->redirect( array( '/dashboard' ) );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all SubJobsite models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN)
       {
          $searchModel = new SubJobsiteSearch();
          $dataProvider = $searchModel->search(Yii::$app->request->queryParams); 
        }
        else
        {
          $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        $searchModel = new SubJobsiteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$filterByJobsite);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SubJobsite model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SubJobsite();

        // set default active
        $model->is_active = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SubJobsite model.
     * If update is successful, the browser will be redirected to the 'view' page.
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

    /**
     * Deletes an existing SubJobsite model.
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
     * Finds the SubJobsite model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SubJobsite the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SubJobsite::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
