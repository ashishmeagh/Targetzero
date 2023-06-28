<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
 use app\helpers\functions;

class SamlController extends \yii\web\Controller
{
    	// Remove CSRF protection
    public $enableCsrfValidation = false;

    public function actions() {
       	
        return [
        	'login' => [
                'class' => 'asasmoyo\yii2saml\actions\LoginAction'
            ],

            'acs' => [
               'class' => 'asasmoyo\yii2saml\actions\AcsAction',
               'successCallback' => [$this, 'callback'],
               'successUrl' => Url::to('adlogin'),
           ],
           'metadata' => [
               'class' => 'asasmoyo\yii2saml\actions\MetadataAction'
           ],
           'logout' => [
               'class' => 'asasmoyo\yii2saml\actions\LogoutAction',
	           'returnTo' => Url::to('home/index'),
                 'parameters' => [],
                 'nameId' => Yii::$app->session->get('nameId'),
                 'sessionIndex' => Yii::$app->session->get('sessionIndex'),
                 'stay' => false,
                 'nameIdFormat' => null,
                 'nameIdNameQualifier' => Yii::$app->session->get('nameIdNameQualifier'),
                 'nameIdSPNameQualifier' => Yii::$app->session->get('nameIdSPNameQualifier'),
                 'logoutIdP' => true, // if you don't want to logout on idp
             ],
             'sls' => [
               'class' => 'asasmoyo\yii2saml\actions\SlsAction',
               'successUrl' => Url::to('home/index'),
               'logoutIdP' => false,
           ]
       ];
   }

    /**
     * @param array $param has 'attributes', 'nameId' , 'sessionIndex', 'nameIdNameQualifier' and 'nameIdSPNameQualifier' from response
     */
    public function callback($param) {
        
            // echo"<pre>";
            // echo var_dump($param);
            // echo"</pre>";
            // exit();
        if (isset($param["isAuthenticated"])  && isset($param["nameId"]) && ($param["isAuthenticated"] == 1)) {
           $session = Yii::$app->session;
           $session->open();
           $session->set( "user.ADlogged", $param["isAuthenticated"] );
           $session->set( "user.ADEmail", $param["nameId"] );
       }
   } 


        /**
         * AD Login
         *
         * @return mixed
         */
        public function actionAdlogin()
        {

         if ( Yii::$app->session->get( "user.ADlogged" ) == 1 )
         {
          $email =  Yii::$app->session->get( "user.ADEmail" ); 
          $user = Yii::$app->db->createCommand("SELECT * FROM [user] WHERE user_name= '$email' AND is_active = 1 AND IsAduser = 1 ")->queryOne();

          if ($user){
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
    else{
        return $this->redirect( array( 'error/access-denied' ) );
    }


}else{
    return $this->redirect( array( 'userlogin/logout' ) );
}


}

}