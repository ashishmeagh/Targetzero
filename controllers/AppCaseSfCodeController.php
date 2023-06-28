<?php

namespace app\controllers;

use Yii;
use app\models\AppCaseSfCode;
use app\models\searches\AppCaseSfCode as AppCaseSfCodeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppCaseSfCodeController implements the CRUD actions for AppCaseSfCode model.
 */
class AppCaseSfCodeController extends AllController
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
     * Lists all AppCaseSfCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppCaseSfCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new AppCaseSfCode model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AppCaseSfCode();
		
		// set default active
		$model->is_active = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            // SF Code Name
            if ( $model->parent_id == NULL )
            {
                $safetyCodeParentName = "-Choose a Safety Code-";
            }
            else
            {
                $safetyCodeParentName = AppCaseSfCode::find()->where( [ "id" => $model->parent_id ] )->one()->code;
            }

            return $this->render( 'create', [
                'model'                => $model,
                'safetyCodeParentName' => $safetyCodeParentName,
            ] );
        }
    }

    /**
     * Updates an existing AppCaseSfCode model.
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
            // SF Code Name
            if ( $model->parent_id == NULL )
            {
                $safetyCodeParentName = "-Choose a Safety Code-";
            }
            else
            {
                $safetyCodeParentName = AppCaseSfCode::find()->where( [ "id" => $model->parent_id ] )->one()->code;
            }

            return $this->render( 'create', [
                'model'                => $model,
                'safetyCodeParentName' => $safetyCodeParentName,
            ] );
        }
    }

    /**
     * Deletes an existing AppCaseSfCode model.
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
     * Finds the AppCaseSfCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppCaseSfCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppCaseSfCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
