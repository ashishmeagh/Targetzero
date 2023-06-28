<?php

namespace app\controllers;

use Yii;
use app\models\ReportTopic;
use app\models\searches\ReportTopic as ReportTopicSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportTopicController implements the CRUD actions for ReportTopic model.
 */
class ReportTopicController extends AllController
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
     * Lists all ReportTopic models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReportTopicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ReportTopic model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReportTopic();
		
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
     * Updates an existing ReportTopic model.
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
     * Deletes an existing ReportTopic model.
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
     * Finds the ReportTopic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReportTopic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportTopic::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
