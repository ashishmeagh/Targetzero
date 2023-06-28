<?php

namespace app\controllers;

use app\components\notification;
use app\helpers\functions;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\Controller;
use app\models\User;
use app\models\searches\AppCase as AppCaseSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\components\issueCreate;
use app\components\sessionData;
use app\components\userData;
use app\components\parametersData;
use app\components\issueData;
use app\components\issueState;
use app\components\issueAssign;
use app\components\comment;
use app\components\attachment;
use app\components\jobsiteData;

/**
 * ApiController manages web services.
 */
class ApiController extends Controller
{
    static $activo = FALSE;
    /*debug*/
    static $logged  = FALSE;
    static $permiso = FALSE;
    /*debug*/
    public $enableCsrfValidation = FALSE;
    static $post_data            = [ ];


    /**
     * Decodea los datos que llegan por post y los guarda en $post_data
     *
     */
    public function __construct( $id, $module, $config = [ ] )
    {
        $post = file_get_contents( "php://input" );
        if ( $post == NULL )
        {
            //            throw new ForbiddenHttpException('Insufficient privileges to access this area.');
            throw new \yii\web\HttpException( 405, 'Wrong method', 405 );
        }

        $encoded_post = Json::decode( $post, TRUE );

        foreach ( $encoded_post as $key => $value )
        {
            if ( $key == "additional_information" || $key == "message" )
            {
                self::$post_data[ $key ] = base64_decode( $value );
            }
            else
            {
                self::$post_data[ $key ] = rawurldecode( $value );
            }
        }

        $this->checkSession();
        $this->checkRole();

        $this->id = $id;
        $this->module = $module;
        parent::__construct( $id, $module, $config = [ ] );
    }

    /**
     * Check session
     * Comprueba si la sesion esta activa
     *
     * Modifica:
     *  $activo, true/false
     */
    public function checkSession()
    {
        $activo = FALSE;
        if ( isset( self::$post_data[ "token" ] ) )
        {
            $token = self::$post_data[ "token" ];
            $activo = sessionData::check( $token );
            self::$logged = TRUE;
        }
        self::$activo = $activo;

        return;
    }

    /**
     * Check role
     * Comprueba si el rol actual puede ver la accion pedida.
     *
     * Modifica:
     *  $activo, true/false
     */
    public function checkRole()
    {
        $activo = FALSE;
        if ( self::$post_data[ "action" ] !== "login" )
        {
            $token = self::$post_data[ "token" ];
            $action = self::$post_data[ "action" ];
            $permission = sessionData::hasPermission( $token, $action );
            if ( $permission != FALSE )
            {
                $activo = TRUE;
                self::$permiso = TRUE;
            }
            self::$activo = $activo;
        }

        return;
    }

    public function checkUpdate()
    {
        return TRUE;
    }

    protected function renderJSON( $data )
    {
        ob_clean(); // clear output buffer to avoid rendering anything else
        header( 'Content-type: application/json' ); // set content type header as json
        exit( json_encode( $data ) );
    }

    /**
     * Action login
     * Crea la sesión y si es necesario, el dispositivo.
     *
     * @param string $_POST['user']
     * @param string $_POST['pass']
     * @param string $_POST['device']
     * @param string $_POST['os']
     * @return mixed {success:"", error:"", token:"",}
     */
    public function actionLogin()
    {
        $password = md5( self::$post_data[ 'pass' ] );
        $Rawusername = self::$post_data[ 'user' ];
        $Rawusername = str_replace("’", "'",$Rawusername);        
        $username = str_replace("'", "''",$Rawusername); 
        $user = userData::getUser( $username, $password );
        if ( strtolower($user[ 'user_name' ]) !== strtolower($Rawusername) || $user[ 'password' ] !== $password || $Rawusername == "" || self::$post_data[ 'pass' ] == "" )
        {
            $response = array(
                'success' => FALSE,
                'error' => "LOGIN_ERR",
            );
        }else if( $user["is_active"] == false ){
            $response = array(
                'success' => FALSE,
                'error' => "USER_INACTIVE",
            );
        }
        else
        {
            try
            {
                $token = sessionData::createSession( self::$post_data[ "device" ], self::$post_data[ "os" ], $user[ "id" ] );
                if ( $token !== NULL )
                {
                    $response = array(
                        'success' => TRUE,
                        'token' => $token,
                        'contractor_id' => $user[ "contractor_id" ],
                    );

                    functions::trackLogin($user["id"], self::$post_data[ "os" ], self::$post_data[ "device" ]);
                }
                else
                {
                    $response = array(
                        'success' => FALSE,
                        'error' => "TOKEN_ERR",
                    );
                }
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_LOGING_IN",
                    'description' => $e,
                );
            }
        }

        $this->renderJSON( $response );
    }

     /**
     * Action login via Email
     *
     * @param string $_POST['user']
     * @param string $_POST['device']
     * @param string $_POST['os']
     * @return mixed {success:"", error:"", token:"",}
     */
    public function actionLoginviaemail()
    {
        $Rawusername = self::$post_data[ 'user' ];
        $Rawusername = str_replace("’", "'",$Rawusername);        
        $username = str_replace("'", "''",$Rawusername); 
        $user = userData::getUserbyemail( $username);
        
        if (!isset($user->user_name) || strtolower($user->user_name) !== strtolower($Rawusername)  || $Rawusername == "")
        {
            $response = array(
                'success' => FALSE,
                'error' => "LOGIN_ERR",
            );
        }else if( $user->is_active == false ){
            $response = array(
                'success' => FALSE,
                'error' => "USER_INACTIVE",
            );
        }
        else
        {
            try
            {
                $token = sessionData::createSession( self::$post_data[ "device" ], self::$post_data[ "os" ], $user->id  );
                
                if ( $token !== NULL )
                {
                    $response = array(
                        'success' => TRUE,
                        'token' => $token,
                        'contractor_id' => $user->contractor_id,
                    );

                    functions::trackLogin($user->id, self::$post_data[ "os" ], self::$post_data[ "device" ]);
                }
                else
                {
                    $response = array(
                        'success' => FALSE,
                        'error' => "TOKEN_ERR",
                    );
                }
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_LOGING_IN",
                    'description' => $e,
                );
            }
        }

        $this->renderJSON( $response );
    }

    /**
     * Action logout
     * Elimina la sesion activa.
     *
     * Recibe:
     *  $_POST['token']
     *  $_POST['action']
     * @return mixed {success:"", error:""}
     */
    public function actionLogout()
    {
        $activo = self::$activo;
        if ( $activo != 1 || $activo != TRUE || $activo = FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",

            );
        }
        else
        {
            try
            {
                sessionData::deleteSession( self::$post_data[ "token" ] );
                $response = array(
                    'success' => TRUE,
                );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_LOGING_OUT",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action parameters
     * Busca todos los parametros presentes en los casos, para los selects de la carga de nuevos casos
     * Recibe:
     *  $_POST['token']
     *  $_POST['action']
     */
    public function actionParameters()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = parametersData::getParameters( self::$post_data[ "token" ] );
            }
            catch ( Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_GETTING_PARAMETERS",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action users
     * Lista de usuarios on demand
     * Recibe:
     *  $_POST['token']
     *  $_POST['action']
     */
    public function actionUsers()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = parametersData::getUsers( self::$post_data[ "token" ] );
            }
            catch ( Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_GETTING_USERS",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action profile
     * Carga los datos personales del usuario de la sesión activa
     * Recibe:
     *  $_POST['token']
     *  $_POST['action']
     * @return mixed {success:"", error:"", "user_name":"", "first_name":"", "last_name":"", "company_name":"", "email":"", "phone":"", "division":"", "employee_number":"" }
     */
    public function actionProfile()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $user = userData::getProfileByToken( self::$post_data[ "token" ] );
                $response = array(
                    'success' => TRUE,
                    'user_id' => $user[ "id" ],
                    'user_name' => $user[ "user_name" ],
                    'first_name' => $user[ "first_name" ],
                    'last_name' => $user[ "last_name" ],
                    'role' => $user[ "role" ],
                    'email' => $user[ "email" ],
                    'phone' => $user[ "phone" ],
                    'division' => $user[ "division" ],
                    'employee_number' => $user[ "employee_number" ],
                );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_GETTING_PROFILE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action cases
     * Carga los casos existentes
     * Recibe:
     *  $_POST['token']
     *  $_POST['action']
     * @return mixed { json }
     */
    public function actionCases()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = issueData::getCases( self::$post_data[ "token" ] );
                sessionData::lastUpdate( self::$post_data[ "token" ] );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_GETTING_CASES",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action Create issue
     * Crea un issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (issueCreate)
     *  $_POST['issueData']
     * @return mixed { json }
     */
    public function actionIssueCreate()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
               
                $response = issueCreate::create( self::$post_data );
            }
            catch ( Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_CREATING_ISSUE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action Close issue
     * Cierra un issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (issueClose)
     *  $_POST['app_case_id']
     * @return mixed { json }
     */
    public function actionIssueClose()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = issueState::close( self::$post_data );
            }
            catch ( Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_CLOSING_ISSUE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action Reopen issue
     * Reabre un issue cerrado
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (issueReopen)
     *  $_POST['app_case_id']
     * @return mixed { json }
     */
    public function actionIssueReopen()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = issueState::reopen( self::$post_data );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_REOPENING_ISSUE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /*
     * Action assign issue
     * Agrega un usuario a los followers de un issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (issueAssign)
     *  $_POST['app_case_id']
     *  $_POST['users_id'] (array)
     * Devuelve:
     * {
            json
        }
     */
    //    public function actionIssueAssign()
    //    {
    //        $activo = self::$activo;
    //        if($activo != true || $activo == false)
    //        {
    //            $response = array(
    //                'success' => FALSE,
    //                'error'   => "SESSION_ERR",
    //            );
    //        }else{
    //            try
    //            {
    //                $response = issueAssign::assign(self::$post_data["app_case_id"], self::$post_data["users_id"]);
    //            }catch(Exception $e){
    //                header("HTTP/1.1 200 OK");
    //                $response = array(
    //                    'success' => FALSE,
    //                    'error'   => "FAILED_ASSIGNING_ISSUE",
    //                    'description'   => $e,
    //                );
    //            }
    //        }
    //        $this->renderJSON( $response );
    //    }

    /**
     * Action assign issue
     * Cambia el creator_id por otro
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (issueAssign)
     *  $_POST['app_case_id']
     *  $_POST['employee_number']
     * @return mixed { json }
     */
    public function actionIssueAssign()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = issueAssign::assignCreator( self::$post_data[ "token" ], self::$post_data[ "app_case_id" ], self::$post_data[ "employee_number" ] );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_ASSIGNING_ISSUE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

     /**
     * Action comment
     * Crea un comentario en el issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (comment)
     *  $_POST['app_case_id']
     *  $_POST['report_type_id']
     *  $_POST['message'] (base64)
     * @return mixed { response: true/false }
     */
    public function actionComment()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = comment::createComment( self::$post_data[ "token" ], self::$post_data[ "app_case_id" ], self::$post_data[ "report_type_id" ], self::$post_data[ "message" ] );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "FAILED_COMMENTING_ISSUE",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action attach
     * Sube una imagen asociada a un issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (attach)
     *  $_POST['app_case_id']
     *  $_POST['photo'] (base64)
     * @return mixed { response: true/false }
     */
    public function actionAttach()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            $se_puede = jobsiteData::photoAllowed( self::$post_data[ "app_case_id" ] );
            
            $response = issueData::getCase(self::$post_data[ "app_case_id" ]);
            $affected_user_id = $response['affected_user_id'];
            $jobsite_id = $response['jobsite_id'];
            $reptoffeder = false;
            $searchModel = new AppCaseSearch();
            $offenderuserandissues = $searchModel->CheckRepeatoffenderissues($affected_user_id,$jobsite_id);
            $reptoffeder = (count($offenderuserandissues) > 0) ? true: false;

            if ( $se_puede ):
                try
                {
                    $response = attachment::attach( self::$post_data[ "token" ], self::$post_data[ "app_case_id" ], self::$post_data[ "photo" ] );

		                if($response["success"] == 1)
		              {
		                  $filepath = $response["file_url"];
		                  notification::notifyForMobileForNewAttachment( self::$post_data[ "app_case_id" ], true, $filepath, $reptoffeder);
		              }  
		        }
                catch ( \Exception $e )
                {
                    header( "HTTP/1.1 200 OK" );
                    $response = array(
                        'success' => FALSE,
                        'error' => "PHOTO_UPLOAD_ERR",
                        'description' => $e,
                    );
                }
            else:
                $response = array(
                    'success' => FALSE,
                    'error' => "PHOTO_UPLOAD_ERR",
                    'description' => "Photo upload not allowed in current jobsite",
                );
            endif;
        }
        $this->renderJSON( $response );
    }

    /**
     * Action get attachments
     * Devuelve las rutas de los attachments de un issue
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (get attachments)
     *  $_POST['app_case_id']
     * @return mixed { response: json de urls de imagenes }
     */
    public function actionGetAttachment()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = attachment::getAttachments( self::$post_data[ "app_case_id" ] );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "GET_ATTACHMENTS_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action get resources
     * Devuelve la lista de recursos externos como url`s
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (parameters)
     * @return mixed { response: json de urls de imagenes }
     */
    public function actionResources()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = parametersData::getResources( self::$post_data[ "token" ] );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "GET_RESOURCES_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action set default jobsite
     * Devuelve la lista de recursos externos como url`s
     * Recibe:
     *  $_POST['token']
     *  $_POST['action'] (parameters)
     */
    public function actionDefaultJobsite()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = userData::setDefaultJobsite( self::$post_data[ "token" ], self::$post_data[ "default_jobsite" ]  );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "SET_DEFAULT_JOBSITE_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
    * Action change password
    * Cambia el password del usuario
    * Recibe:
    * $_POST['token']
    * $_POST['old_password']
    * $_POST['new_password']
    * @return mixed { response: 1/0 }
    */
    public function actionChangePassword()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = userData::changePassword( self::$post_data[ "token" ], self::$post_data[ "old_password" ], self::$post_data[ "new_password" ]  );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "CHANGE_PASSWORD_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action reset password
     * Cambia el password del usuario a partir de un email
     * Recibe:
     * $_POST['email']
     * @return mixed { response: 1/0 }
     */
    public function actionResetPassword()
    {
        try
        {
            $response = userData::resetPassword( self::$post_data[ "email" ] );
        }
        catch ( \Exception $e )
        {
            header( "HTTP/1.1 200 OK" );
            $response = array(
                'success' => FALSE,
                'error' => "CHANGE_PASSWORD_ERR",
                'description' => $e,
            );
        }
        $this->renderJSON( $response );
    }

    /**
     * Send a newsflash
     * Envia un issue en broadcast a todos los usuarios de wt
     * Recibe:
     * $_POST['email']
     * @return mixed { response: 1/0 }
     */
    public function actionSendNewsflash()
    {
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                notification::newsflash( self::$post_data[ "app_case_id" ] );
                $response = array(
                    'success' => true
                );
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "NEWSFLASH_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    /**
     * Action version
     * Devuelve las version actual de los WS
     */
    public function actionVersion()
    {
        header( "HTTP/1.1 200 OK" );
        $response = array(
            'success' => true,
            'wsVersion' => CURRENT_VERSION
        );
        $this->renderJSON( $response );
    }


    /*
     EXCLUSIVO MIGRACION DE ISSUES
     */
    /*
{
  "token":"kimI0Vs8Uo9pZMniKt0iPBUL3UqcxFcgfz4UUEO4HJvSB",
  "action" : "issue create",
  "affected_user_employee_number" : "99",
  "building_id" : "1",
  "app_case_type_id" : "1",
  "app_case_sf_code_id" : "1",
  "app_case_priority_id" : "1",
  "additional_information" : "SGVyZSBpcyB0aGUgYWRkaXRpb25hbCBpbmZvcm1hdGlvbg==",
  "trade_id" : "1",
  "foreman_id" : "1",
  "correction_date" : "2015-05-22 19:57:42"
}
    */

    public function actionMigrarIssues()
    {
        $response = issueCreate::create2( self::$post_data );
        $this->renderJSON( $response );
    }
    
    public function actionPhotoUpload()
    {

            
        $this->checkSession();
        $activo = self::$activo;
        if ( $activo != TRUE || $activo == FALSE )
        {
            $response = array(
                'success' => FALSE,
                'error' => "SESSION_ERR",
            );
        }
        else
        {
            try
            {
                $response = attachment::SaveAttachmentsForMobile( self::$post_data );
                
            }
            catch ( \Exception $e )
            {
                header( "HTTP/1.1 200 OK" );
                $response = array(
                    'success' => FALSE,
                    'error' => "PHOTOUPLOAD_ERR",
                    'description' => $e,
                );
            }
        }
        $this->renderJSON( $response );
    }

    

}
