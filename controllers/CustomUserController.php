<?php

namespace app\controllers;

use app\models\NewUserSelfRegistration;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\helpers\emailValidation;
use app\models\User;
use app\models\Role;
use app\components\notification;
use yii\debug\models\search\Log;
use app\models\UserJobsite;

class CustomUserController extends \yii\web\Controller {
    // Properties
    public $layout = 'custom-user';

    /**
     * Before Action
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action) {
        $this->enableCsrfValidation = FALSE;
        return parent::beforeAction($action);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex($en) {
       $rdata = base64_decode($en);
        $checkvalidity = explode("@", $rdata);
        /*$model = new NewUserSelfRegistration();
            // User Post Data
            $postData = Yii::$app->request->post();

            return $this->render('index', [
                'model' => $model
            ]);*/

        $sqlQuery = "SELECT jobsite_id
                        FROM [dbo].[qr_code] 
                        WHERE is_active = 1 AND jobsite_id = " . $checkvalidity[0] . "AND secretcode = '" . $en . "'";

        $qr_code_exists = Yii::$app
            ->db
            ->createCommand("$sqlQuery")->queryAll();

        $storejobsiteID = (isset($qr_code_exists[0]['jobsite_id'])) ? $qr_code_exists[0]['jobsite_id'] : 0;

        if (!isset($en) || $en == '' || count($checkvalidity) <= 1)
        {

            return $this->redirect(Yii::$app
                ->request->baseUrl . '/custom-user/unauthorised');
        }
        else if (!$checkvalidity[0] || !(is_numeric($checkvalidity[0])) || $checkvalidity[0] <= 0 || !(is_numeric($storejobsiteID)) || $storejobsiteID <= 0)
        {

            return $this->redirect(Yii::$app
                ->request->baseUrl . '/custom-user/unauthorised');

        }
        else
        {
            $model = new NewUserSelfRegistration();
            // User Post Data
            $postData = Yii::$app
                ->request
                ->post();
            $sqlQuery = "SELECT C.id as id, C.contractor as contractor,  j.[jobsite] + isnull('-'+ j.job_number, '') as jobsitename 
                        FROM [dbo].[contractor]  C
                        INNER JOIN [dbo].[contractor_jobsite] CJ on CJ.contractor_id = C.id
                        LEFT JOIN [dbo].[jobsite] j on j.id = CJ.jobsite_id
                        WHERE C.is_active = 1 AND CJ.jobsite_id = " . $checkvalidity[0] . " group by C.contractor, C.id,C.id,j.[jobsite],j.job_number ORDER BY contractor ASC";
                     
            $data_creator = Yii::$app
                ->db
                ->createCommand("$sqlQuery")->queryAll();
            $ContractorsListdataArray = array();
            $hidden_roles = [1,6,18];
            $RolesListdataArray = array();
            $rolesArray = Role::find()->where(['not in','id', $hidden_roles])
                                      ->asArray()->all();
            $jobsitename = '';
            foreach ($data_creator as $value)
            {
                $temp['value'] = $value['contractor'];
                $temp['data'] = $value['id'];
                $jobsitename = $value['jobsitename'];
                array_push($ContractorsListdataArray, $temp);
            }
            foreach ($rolesArray as $value)
            {
                $temp['value'] = $value['role'];
                $temp['data'] = $value['id'];
                
                array_push($RolesListdataArray, $temp);
            }

            /*$temp['value'] = 'Whiting-Turner Contracting Co';
                      $temp['data'] = 148;
                      array_push($ContractorsListdataArray, $temp); */

            return $this->render('index', ['model' => $model, 'jobsite_id' => $checkvalidity[0], 'contractor_arr' => $ContractorsListdataArray, 'jobsitename' => $jobsitename, 'role_arr' => $RolesListdataArray]);
        }

    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate() {

        $modelNewUserRegister = new NewUserSelfRegistration();
        // set default active
        // $model->is_active = 1;
        // User Post Data
        $postData = Yii::$app
            ->request
            ->post();

        if ($postData["User"]["contractor_id"] == 148){
           $sql = "Select 1 From (Select employee_number From [dbo].[user] Where employee_number = '" . $postData['User']['employee_number'] . "' AND user_name = '" . $postData['User']['user_name'] . "' AND default_jobsite = " . $postData["User"]["jobsite"] . ") As Emplynumber";
        }else{
           $sql = "Select 1 From (Select employee_number From [dbo].[user] Where employee_number = '" . $postData['User']['employee_number'] . "' AND default_jobsite = " . $postData["User"]["jobsite"] . " UNION Select employee_number From [dbo].[new_user_self_registration] Where employee_number = '" . $postData['User']['employee_number'] . "' AND  jobsite_id = " . $postData["User"]["jobsite"] . " ) As Emplynumber";
        }    
       

        $recordAlreadyExists = Yii::$app
            ->db
            ->createCommand($sql)->queryAll();

        if ($recordAlreadyExists)
        {
            exit("-1");
        }
        else
        {
            $email = $postData["User"]["email"];
            $modelNewUserRegister->jobsite_id = $postData["User"]["jobsite"];
            $modelNewUserRegister->role_id = $postData["User"]["role_id"];
            $modelNewUserRegister->first_name = $postData["User"]["first_name"];
            $modelNewUserRegister->last_name = $postData["User"]["last_name"];
            $modelNewUserRegister->emergency_contact = str_replace("-", "", $postData["User"]["emergency_contact"]);
            $modelNewUserRegister->phone = str_replace("-", "", $postData["User"]["phone"]);
            $modelNewUserRegister->emergency_contact_name = $postData["User"]["emergency_name"];
            $modelNewUserRegister->contractor_id = $postData["User"]["contractor_id"];
            $modelNewUserRegister->employee_number = $postData["User"]["employee_number"];
            $modelNewUserRegister->email = $postData["User"]["email"];
            $modelNewUserRegister->agreed = 1;
            $modelNewUserRegister->created = date('Y/m/d H:i:s');
            $modelNewUserRegister->updated = date('Y/m/d H:i:s');
            $modelNewUserRegister->digital_signature = $postData["digital_signature"];
            $modelNewUserRegister->username = $postData["user_name"];
            $modelNewUserRegister->emailverified = null;
            $model_status = $modelNewUserRegister->save();

            if ($model_status)
            { 
                //exit('inside');
                $last_insertid = Yii::$app
                    ->db
                    ->getLastInsertId();
                if ($postData["User"]["contractor_id"] == 148)
                {    
                       $emailvalidation = new emailvalidation();
                       $emailvalidation->SendEmailValidation($email, (int)$last_insertid);
                }
                else
                {
                  
                    $userdetails = Yii::$app
                        ->db
                        ->createCommand("SELECT [jobsite_id]
          ,[first_name] ,[last_name],[employee_number],[role_id], [contractor_id], [phone], [email], [emergency_contact], [digital_signature], [emergency_contact_name] FROM [dbo].[new_user_self_registration] WHERE id=" . (int)$last_insertid)->queryAll();
                    //$role_id = 14;
                    // $role_id = 15;
                   
                    $role_id = $userdetails[0]['role_id'];
                    $data = $this->CreateNewUser($userdetails, $role_id);
                 
                    
                }
                exit("submitted");
            }
            else
            {
                exit("error");
            }
        }

        return "true";
    }

    /**
     * Verify user.
     *
     * @return mixed
     */
    public function actionEmailVerify($data)
    {
        $verifystatus = "process";

        return $this->render('emailverify', [
                'data' => $data, 'verifystatus'=>$verifystatus
            ]);


    }

    public function actionProcessEmailVerify($data)
    {   $array_nwtRoles = array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17);                
        if(isset($data)){
        $rdata = base64_decode($data);
        $currentdatetime = date('Y/m/d H:i:s');
        $sqlquerycheckexecution = "select 1 from [dbo].[new_user_self_registration] where DATEDIFF(ss, created, '$currentdatetime')>30 and id =". (int)$rdata;
         $recordisvalid = Yii::$app
            ->db
            ->createCommand($sqlquerycheckexecution)->queryAll();

        if($recordisvalid){
        $sqlQuery = "update [dbo].[new_user_self_registration]  set emailverified = 1 where id=" . (int)$rdata;
        $data_creator = Yii::$app
            ->db
            ->createCommand("$sqlQuery")->execute();
        $userdetails = Yii::$app
            ->db
            ->createCommand("SELECT [jobsite_id]
      ,[first_name] ,[last_name],[employee_number],[role_id], [contractor_id],[emergency_contact], [digital_signature], [emergency_contact_name] ,[phone],[email], [username]  As username FROM [dbo].[new_user_self_registration] WHERE id=" . (int)$rdata)->queryAll();
        //$role_id = 2;
        // $role_id = 15;
        $role_id = $userdetails[0]['role_id'];
        $data = $this->CreateNewUser($userdetails, $role_id);
        
        if ($data == "true")
        {
            $msg = "Your email was successfully verified. You will receive an email with login details shortly";
            $verifystatus = "true";
        }
        else
        {
           $msg = $data;
           $verifystatus = "false";
        }
      }
    }
        return $this->render('emailverify', [
                'msg' => $msg, 'verifystatus'=>$verifystatus
            ]);

    }

    /**
     * Error page for unauthorised.
     *
     * @return mixed
     */
    public function actionUnauthorised()
    {

        $model = new NewUserSelfRegistration();
        return $this->render('error');

    }

    /**
     * Create A New User
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function CreateNewUser($userdetails, $role_id)
    {

        $model = new User();
        $array_wtRoles = array(1, 2, 3, 4, 5, 6, 7, 8, 15);
        $array_ContrRoles = array(10, 11, 12, 13, 14);
        // set default active
        $model->is_active = 1;
        $sql = "SELECT * from [user] INNER JOIN [dbo].[user_jobsite] as UJ on UJ.user_id =  [user].id WHERE ";
        if (isset($userdetails[0]['contractor_id']))
        {
            $contractor_id = $userdetails[0]['contractor_id'];
            $sql .= "[user].contractor_id = $contractor_id ";
            $model->contractor_id = $contractor_id;
        }
        if (isset($userdetails[0]['jobsite_id']))
        {
            $jobsite_id = (int)$userdetails[0]['jobsite_id'];
            $sql .= "AND UJ.jobsite_id = $jobsite_id ";
        }
        if (isset($role_id))
        {
            $sql .= "AND [user].role_id = $role_id ";
            $model->role_id = $role_id;
        }
        if (isset($userdetails[0]['first_name']))
        {
            $fs = str_replace("'", "''", $userdetails[0]['first_name']);
            // $sql.= "AND [user].first_name = '$fs' ";
            $model->first_name = $fs;
        }
        if (isset($userdetails[0]['last_name']))
        {
            $ls = str_replace("'", "''", $userdetails[0]['last_name']);
            // $sql.= "AND [user].last_name = '$ls' ";
            $model->last_name = $ls;
        }

        if (isset($userdetails[0]['employee_number']))
        {
            $employee_number = str_replace("'", "''", $userdetails[0]['employee_number']);
            $sql .= "AND [user].employee_number = '$employee_number' ";
            $model->employee_number = $userdetails[0]['employee_number'];
        }
        if (isset($userdetails[0]['email']))
            {
                $email = $userdetails[0]['email'];
                $sql .= "AND [user].email = '$email' ";
                $model->email = $email;
            }
        if (in_array($role_id, $array_wtRoles))
        {
            if (isset($userdetails[0]['username']))
            {
                $username = $userdetails[0]['username'];
                $sql .= "AND [user].user_name = '$username' ";
                $model->user_name = $username;
            }
        }
            
            $rand_pass = uniqid();
            $model->password = md5($rand_pass);

        

        $recordAlreadyExists = Yii::$app
            ->db
            ->createCommand($sql)->queryAll();

        if (!$recordAlreadyExists)
        {

            //generate random password
            //$rand_pass = uniqid();

            $model->sop = 1;
            if ($model->contractor_id == 148 || $model->contractor_id == '148')
            {
                $model->IsAduser = 1;
            }

            $model->emergency_contact_name = $userdetails[0]['emergency_contact_name'];
            $model->emergency_contact = $userdetails[0]['emergency_contact'];
			$model->phone = $userdetails[0]['phone'];
            $model->digital_signature = $userdetails[0]['digital_signature'];

            //$model->password = md5($rand_pass);
            //$model->employee_number =  $userdetails[0]['employee_number'];
            

            // User
            $transaction = $model->getDb()
                ->beginTransaction();
            $model_status = $model->save();

            // User-Jobsite Save
            $user_jobsite_status = true;
            if (isset($userdetails[0]['jobsite_id']))
            {

                $user_jobsite_model = new UserJobsite();
                $user_jobsite_model->user_id = $model->id;
                $user_jobsite_model->jobsite_id = (int)$userdetails[0]['jobsite_id'];
                if (!$user_jobsite_model->save())
                {
                    $user_jobsite_status = false;
                    // echo "user jobsite error"; exit();
                    
                }

            }

            if ($model_status && $user_jobsite_status)
            {
              
                $transaction->commit();
                if (in_array($role_id, $array_wtRoles) || in_array($role_id, $array_ContrRoles))
                {
                    notification::notifyNewUser($model->id, $rand_pass);
                }
                notification::notifyNewUserToAdmin($model->id, (int)$userdetails[0]['jobsite_id']);
                
                

                //echo "notified";
                return "true";
            }
            else
            {
                $model->validate();

                return "Error while creating account.  Please contact WT Team.";
            }

        }
        else
        {
            return "This user is already in the system. Contact the Jobsite Admin.";
        }

    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}

