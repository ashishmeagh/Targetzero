<?php

namespace app\controllers;

use app\components\sqlRoleBuilder;
use Yii;
use app\models\Building;
use app\models\searches\Building as BuildingSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BuildingController implements the CRUD actions for Building model.
 */
class BuildingController extends AllController
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

        if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_TRADE_PARTNER)
        {
            return $this->redirect( array( '/dashboard' ) );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all Building models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( 'user.role_id' ) != ROLE_WT_EXECUTIVE_MANAGER )
        {
            $filterByJobsite = sqlRoleBuilder::getBuildingByUserId( Yii::$app->session->get( 'user.id' ) );
        }
        else
        {
            $filterByJobsite = '';
        }
        $searchModel = new BuildingSearch();
        $dataProvider = $searchModel->search( Yii::$app->request->queryParams, $filterByJobsite );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Building();
		
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
     * Updates an existing Building model.
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

    /**
     * Deletes an existing Building model.
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
     * Finds the Building model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Building the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Building::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
