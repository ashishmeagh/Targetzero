<?php

namespace app\controllers;

use Yii;
use app\models\Trade;
use app\models\searches\Trade as TradeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TradeController implements the CRUD actions for Trade model.
 */
class TradeController extends AllController
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
        if( Yii::$app->session->get('user.role_id') != ROLE_ADMIN && Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN )
        {
            return $this->redirect( array( 'app-case/index' ) );
        }else{
            return parent::beforeAction( $action );
        }
    }
    /**
     * Lists all Trade models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TradeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Trade model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Trade();
		
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
     * Updates an existing Trade model.
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
     * Deletes an existing Trade model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{
			if( $this->findModel($id)->delete() ){
				$data = true;
			}else{
				$data = false;
			}
		}catch( \Exception $ex ){
			$data = false;
		}
        
		header( 'Content-type: application/json' );
        exit( json_encode( $data ) );
    }

    /**
     * Finds the Trade model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Trade the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Trade::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
