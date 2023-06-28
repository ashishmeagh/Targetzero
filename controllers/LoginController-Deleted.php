<?php

    namespace app\controllers;

    use app\helpers\functions;
    use Yii;
    use app\models\User;
    use app\models\FormResetPass;
	  use app\components\notification;

    class LoginController extends \yii\web\Controller
    {
        // Properties
        public $layout = 'login';

        /**
         * Comprueba si el usuario esta logueado / hace el login de usuario.
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
                $model = new User();
                $model->load( Yii::$app->request->post() );

                if ( Yii::$app->request->post() )
                {
					          $password = md5( Yii::$app->request->post( "User" )[ "password" ] );

//                    $user = $model->find()->where( [ "user_name" => Yii::$app->request->post( "User" )[ "user_name" ], "password"  => $password])->andWhere("is_active = 1 ")->one();
                    $username = Yii::$app->request->post( "User" )[ "user_name" ];
                    $username = str_replace("'", "''",$username);
                    $user = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE user_name= '$username' AND password = '$password' AND is_active = 1 AND role_id not in (10,11,12,13,14) ")->queryOne();

                    if ( $user && Yii::$app->request->post( "User" )[ "password" ] != "" && Yii::$app->request->post( "User" )[ "user_name" ] != "" )
                    {

                        //¿Se marcó "keep me logged in"?
                        if(isset($_POST['remember'])){
                          setcookie("rememberMe", '{"user.id":"'.$user["id"].'","user.username":"'.$user["user_name"].'","user.first_name":"'.$user["first_name"].'","user.last_name":"'.$user["last_name"].'","user.contractor_id":"'.$user["contractor_id"].'","user.role_id":"'.$user["role_id"].'"}', time() + (86400 * 30), "/"); // 86400 = 1 day
                        }else{
							 setcookie("rememberMe", '{"user.id":"'.$user["id"].'","user.username":"'.$user["user_name"].'","user.first_name":"'.$user["first_name"].'","user.last_name":"'.$user["last_name"].'","user.contractor_id":"'.$user["contractor_id"].'","user.role_id":"'.$user["role_id"].'"}', time() + (86400 * 30), "/"); // 86400 = 1 day
                         // setcookie("rememberMe", "", -1, "/");
                        //  unset($_COOKIE['rememberMe']);
                        };

                        $session = Yii::$app->session;
                        $session->open();

                        $session->set( "user.logged", "true" );
                        $session->set( "user.id", $user["id"] );
                        $session->set( "user.full_name", $user["first_name"] . " " . $user["last_name"] );
                        $session->set( "user.username", $user["user_name"] );
                        $session->set( "user.contractor_id", $user["contractor_id"] );
                        $session->set( "user.role_id", $user["role_id"] );
                        $session->set( "processing", "false" );
                        functions::trackLogin($user["id"], 'desktop');

                        if (Yii::$app->session->get( "user.role_id" ) == ROLE_CLIENT_MANAGER)
                        {
                            return $this->redirect( array( '/dashboard' ) );
                        }else{
                            return $this->redirect( array( 'app-case/index' ) );
							
                        }
                    }
                    else
                    {
//                        Yii::$app->getSession()->setFlash( 'error', 'Your Text Here..' );
                        $model->addError( 'user_name', "Login error/Invalid user" );
                        $model->addError( 'password', "Incorrect username or password" );
                    }
                }

                return $this->render( 'index', [ 'model' => $model ] );
            }
        }

        /**
         * Borra y elimina la sesion del usuario logueado
         *
         * @return mixed
         */
        public function actionLogout()
        {
            $session = Yii::$app->session;
            $session->open();
            $session->destroy();
            $session->close();
            //Eliminar cookie de "Keep me logged in"
            setcookie("rememberMe", "", -1, "/");
            unset($_COOKIE['rememberMe']);
            $this->redirect( array( 'login/index' ) );
        }

        /**
         * Resetea el password del usuario basado en el email.
         *
         * @return mixed
         */
		public function actionResetpassword(){
			$model = new FormResetPass();

			if ($model->load(Yii::$app->request->post())){
				if ($model->validate()){

					//get user by email
					$user_table = User::find()->where(['email' => $model->email])->one();
					//generate random password
					$rand_pass = uniqid();
					//set password
					$user_table->password = md5( $rand_pass );

					if ($user_table->save()){
						notification::notifyRecovery( $user_table->id, $rand_pass );
						return $this->redirect(["login/index"]);
					}

				}else{
					$model->getErrors();
				}
			}

			return $this->render( 'resetpassword', [ 'model' => $model ] );
		}
    }
