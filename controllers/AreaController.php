<?php

namespace app\controllers;

use app\components\sqlRoleBuilder;
use Yii;
use app\models\Area;
use app\models\Floor;
use app\models\Building;
use app\models\searches\Area as AreaSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AreaController implements the CRUD actions for Area model.
 */
class AreaController extends AllController
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
     * Lists all Area models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( 'user.role_id' ) != ROLE_WT_EXECUTIVE_MANAGER )
        {
            $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        }
        else
        {
            $filterByJobsite = '';
        }
        $searchModel = new AreaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterByJobsite);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Area model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Area();
		
		// set default active
		$model->is_active = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            // Data
            $data = array();
            $data['jobsite_id'] = Yii::$app->request->post('jobsite_id', '');
            $data['building_id'] = Yii::$app->request->post('building_id', '');

            return $this->render( 'create', [
                'model' => $model,
                'data'  => $data,
            ] );
        }
    }

    /**
     * Updates an existing Area model.
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
            $data['building_id'] = Floor::find()->select('building_id')->where(['id' => $model->floor_id])->one()->building_id;
			$data['jobsite_id'] = Building::find()->select('jobsite_id')->where(['id' => $data['building_id']])->one()->jobsite_id;

            return $this->render( 'update', [
                'model' => $model,
                'data'  => $data,
            ] );
        }
    }

    /**
     * Deletes an existing Area model.
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
     * Finds the Area model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Area the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Area::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
