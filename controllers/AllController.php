<?php

    namespace app\controllers;

    use Yii;

    class AllController extends \yii\web\Controller
    {

        /**
         * Before Action
         * Chequeo en cada acción la sesion del usuario y los roles
         *
         * @param \yii\base\Action $action
         *
         * @return bool|\yii\web\Response
         * @throws \yii\web\BadRequestHttpException
         */
        public function beforeAction( $action )
        {
            // return parent::beforeAction( $action );
            //        print_r( Yii::$app->controller->id."\n" );
            //        print_r( Yii::$app->controller->action->id );

            // Chequear si el usuario está en sesión o la cookie rememberMe es válida para loguear automáticamente
            if ( Yii::$app->session->get( "user.logged" ) === "true" || isset($_COOKIE['rememberMe']) )
            {
                if(isset($_COOKIE['rememberMe'])){
                  $cookieUser = json_decode($_COOKIE['rememberMe'], true);
                  //var_dump($_COOKIE['rememberMe']); exit();
                  $session = Yii::$app->session;
                  $session->open();
                  $session->set( "user.logged", "true" );
                  $session->set( "user.id", $cookieUser["user.id"] );
                  $session->set( "user.full_name", $cookieUser["user.first_name"] . " " . $cookieUser["user.last_name"] );
                  $session->set( "user.username", $cookieUser["user.username"] );
                  $session->set( "user.contractor_id", $cookieUser["user.contractor_id"] );
                  $session->set( "user.role_id", $cookieUser["user.role_id"] );
                  $session->set( "processing", "false" );

                }
                if(Yii::$app->session->get( "RedirectURL" ) !== NULL)
                {  
                  $session = Yii::$app->session;
                  $session->open();
                  $redirectURL = Yii::$app->session->get("RedirectURL");
                  $session->set( "RedirectURL", NULL);           
                  $this->redirect($redirectURL);                  
                  return false;
                }else {
                return parent::beforeAction( $action );
            }
                
            }
            else
            {
                $session = Yii::$app->session;
                $session->open();
                $session->set( "RedirectURL", Yii::$app->request->url );
                $this->redirect(['home/index']);
                
                return false;
            }
        }
    }
