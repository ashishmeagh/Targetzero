<?php

    namespace app\controllers;

    use app\helpers\functions;
    use Yii;
    use app\models\User;
    use app\models\FormResetPass;
	use app\components\notification;

    class HomeController extends \yii\web\Controller
    {
        // Properties
        public $layout = 'pre-login';

        /**
         *Home Controller
         *
         * @return mixed
         */
        public function actionIndex()
        {
            if ( Yii::$app->session->get( "user.logged" ) === "true" )
            {
                if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER)
                {
                    return $this->redirect( array( '/dashboard' ) );
                }else{
                    return $this->redirect( array( 'app-case/index' ) );
                }
            }
            else
            {               
                return $this->render( 'index');
            }
        }

        
    }
