<?php

namespace app\controllers;

use app\components\sqlRoleBuilder;
use Yii;
use app\models\Floor;
use app\models\Building;
use app\models\searches\Floor as FloorSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FloorController implements the CRUD actions for Floor model.
 */
class FloorController extends AllController
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

        if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_WT_SAFETY_PERSONNEL  || Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_SAFETY_PERSONNEL || Yii::$app->session->get( "user.role_id" ) == ROLE_TRADE_PARTNER)
        {
            return $this->redirect( array( '/dashboard' ) );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all Floor models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( 'user.role_id' ) != ROLE_WT_EXECUTIVE_MANAGER )
        {
            $filterByJobsite = sqlRoleBuilder::getFloorsByUserId( Yii::$app->session->get( 'user.id' ) );
        }
        else
        {
            $filterByJobsite = '';
        }
        $searchModel = new FloorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterByJobsite );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Floor model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Floor();
		
		// set default active
		$model->is_active = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            // Data
            $data = array();
            $data['jobsite_id'] = Yii::$app->request->post('jobsite_id', '');

            return $this->render( 'create', [
                'model' => $model,
                'data'  => $data,
            ] );
        }
    }

    /**
     * Updates an existing Floor model.
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
            // Data
            $data = array();
            $data['jobsite_id'] = Building::find()->select('jobsite_id')->where(['id' => $model->building_id])->one()->jobsite_id;

            return $this->render( 'update', [
                'model' => $model,
                'data'  => $data,
            ] );
        }
    }

    /**
     * Deletes an existing Floor model.
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
     * Finds the Floor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Floor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Floor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
