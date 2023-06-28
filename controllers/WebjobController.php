<?php

namespace app\controllers;

use app\components\aduserData;
use app\models\User;
use app\models\Contractor;
use app\models\JobErrors;
use app\components\notification;
use Yii;

ini_set('max_execution_time', 0);
/**
 * WebjobController implements web job to pull users from AD.
 */
class WebjobController extends \yii\web\Controller {

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
     * Web Job for pulling Ad users.
     * @param mixed $sourceid Sourceid
     * @return mixed
     */
    public function actionWebjobadusers($sourceid) {

        $Adusers = new aduserData();
        $dataProvider = $Adusers->getAdUsersProfile($sourceid);
        $this->renderJSON($dataProvider);

    }

    /**
     * Web Job Sync TZ WT users with AD users.
     * @param mixed $sourceid Sourceid
     * @return mixed
     */
    public function actionWebjobsyncusers() {

        $Adusers = new aduserData();
        $dataProvider = $Adusers->UpdateUsersDetails();
        $this->renderJSON($dataProvider);

    }

    /**
     * Web Job Sync TZ WT Craftmen users Creation.
     * @return mixed
     */
    public function actionWebjobwtcraftmenusers() {

      $model = new User();

       $userdetails_array = Yii::$app->db->createCommand( "select  CE.FIRST_NAME as first_name,CE.LAST_NAME as last_name,CE.EMAIL_ADD as email,LTRIM(RTRIM(CE.EMP_NO)) as employee_number, 148 as contractor_id from [dbo].[CMIC_EMP] CE where CE.PAYRUN_GROUP = 'WKSP' and CE.EMP_NO not in (select u.employee_number from dbo.[user] u where u.role_id = 19 and u.is_active = 1)" )->queryAll();

        $role_id = 19;

        foreach ( $userdetails_array as $userdetails )
        {
             $data = $this->CreateNewUser($userdetails, $role_id);  
             if($data == "true"){
          echo "User created success fully"; 
        }else{
          echo $data;
        }
        }

       exit('Web job completed');
    }

    /**
     * Web Job Sync TZ WT Contractor Creation.
     * @return mixed
     */
    public function actionWebjobwtcontractorcreation() {
       
      $model = new User();
      $result ='Web job completed'."</br>";

       //$con_details_array = Yii::$app->db->createCommand( "select TOP 500 BP.BP_NAME as contractor,BP.ADD1 + ','+ISNULL(BP.ADD2, '')+ ','+ISNULL(BP.ADD3, '')+ ','+ISNULL(BP.STATE_CODE, '') + ','+ISNULL(BP.POSTAL_CODE, '')as con_address, BP.BP_CODE as vendornumber from  [dbo].[CMIC_BPARTNERS] BP where BP.BP_NAME not in (select c.contractor from [dbo].[contractor] c where c.is_active = 1) and BP.BP_ACTIVE = 'Y'" )->queryAll();
        $con_details_array = Yii::$app->db->createCommand( "select DISTINCT  TOP 500 BP.BP_NAME as contractor,BP.ADD1 + ','+ISNULL(BP.ADD2, '')+ ','+ISNULL(BP.ADD3, '')+ ','+ISNULL(BP.STATE_CODE, '') + ','+ISNULL(BP.POSTAL_CODE, '')as con_address, BP.BP_CODE as vendornumber from [dbo].[CMIC_BPARTNERS] BP 
        Left Join [dbo].[contractor] c on c.contractor = trim(BP.BP_NAME)  and c.cmic_updated is not null and is_active = 1  where BP.BP_ACTIVE = 'Y' and c.vendor_number is null and BP.BP_CODE not in ('136837')" )->queryAll();
             
        foreach ( $con_details_array as $con_details )
        {
            
             $data = $this->CreateNewContractor($con_details);  
             
             if($data == "true"){
             $result .= $con_details['contractor']." -Contractor created success fully"."</br>"; 
		        }else{
		          $result .= $data."</br>";
		        }
        }

              exit($result);
    }
 
    /**
     * Web Job Sync TZ WT Contractor Creation.
     * @return mixed
     */
    public function actionWebjobwtcontractorupdation() {
       
      $model = new User();

       $con_details_array = Yii::$app->db->createCommand( "select c.id, BP.BP_NAME as contractor,BP.ADD1 + ','+ISNULL(BP.ADD2, '')+ ','+ISNULL(BP.ADD3, '')+ ','+ISNULL(BP.STATE_CODE, '') + ','+ISNULL(BP.POSTAL_CODE, '')as con_address, BP.BP_CODE as vendornumber from  [dbo].[CMIC_BPARTNERS] BP Inner Join [dbo].[contractor] c on c.contractor = BP.BP_NAME and c.is_active = 1  and c.cmic_updated is not null where BP.BP_ACTIVE = 'Y'" )->queryAll();

        
        foreach ( $con_details_array as $con_details )
        {
            
             $data = $this->updateContractor($con_details);  
             
             if($data == "true"){
             echo $con_details['contractor']." -Contractor created success fully"; 
                }else{
                  echo $data;
                }
        }
exit('Web job completed');

    }

    /**
     * Create A New Contractor
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function CreateNewContractor($contractordetails) {
        $model = new Contractor();

		// set default active
		$model->is_active = 1;
        
                
        if ($contractordetails) {

            $sql = "SELECT * from [contractor] WHERE cmic_updated is not null ";
            if(isset($contractordetails['contractor'])){
              $sql.= "AND [contractor].contractor = '".str_replace("'", "''",$contractordetails['contractor'])."'";
            }
            
            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();
            if($recordAlreadyExists){
              return $contractordetails['contractor']." - Contractor account already exits. Please contact WT Team.";
            }

            date_default_timezone_set("America/Chicago");
            $model->cmic_updated = date("Y-m-d H:i:s");
            $model->contractor = $contractordetails['contractor'];
            $model->address = $contractordetails['con_address'];
            $model->vendor_number = $contractordetails['vendornumber'];
            
            
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();
            $id = Yii::$app->db->getLastInsertID();

            if($model_status) 
            {
                $transaction->commit();
                return "true";
            }
            else
            {
                return $contractordetails['contractor']." - Error while creating Contractor account. Please contact WT Team.";
            }

        } else {
             return $contractordetails['contractor']." -Contractor account already exits. Please contact WT Team.";
        }
    }

    /**
     * Create A New Contractor
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function updateContractor($contractordetails) {
       
        $model = $this->findContractorModel($contractordetails['id']);
        // set default active
        $model->is_active = 1;

                
        if ($contractordetails) {

            date_default_timezone_set("America/Chicago");
            $model->cmic_updated = date("Y-m-d H:i:s");
            $model->contractor = $contractordetails['contractor'];
            $model->address = $contractordetails['con_address'];
            $model->vendor_number = $contractordetails['vendornumber'];
            
            
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();

            if($model_status) 
            {
                $transaction->commit();
                return "true";
            }
            else
            {
                return $contractordetails['contractor']." - Error while creating Contractor account. Please contact WT Team.";
            }

        } else {
             return $contractordetails['contractor']." -Contractor account already exits. Please contact WT Team.";
        }
    }

    /**
     * Create A New User
     *
     * @param string $value
     *
     * @return bool|string
     */
    protected function CreateNewUser($userdetails, $role_id) {
       $model = new User();

        // set default active
        $model->is_active = 1;
            $sql = "SELECT * from [user] INNER JOIN [dbo].[user_jobsite] as UJ on UJ.user_id =  [user].id WHERE ";
            if(isset($userdetails['contractor_id'])){
              $contractor_id = $userdetails['contractor_id'];
              $sql.= "[user].contractor_id = $contractor_id ";
              $model->contractor_id = $contractor_id;
            }
            if(isset($userdetails['jobsite_id'])){
             $jobsite_id = (int)$userdetails['jobsite_id'];
              $sql.= "AND UJ.jobsite_id = $jobsite_id ";
            }
            if(isset($role_id)){
              $sql.= "AND [user].role_id = $role_id ";
              $model->role_id = $role_id;
            }
            if(isset($userdetails['first_name'])){
              $fs = str_replace("'", "''",$userdetails['first_name']);
             // $sql.= "AND [user].first_name = '$fs' ";
              $model->first_name = $fs;
            }
            if(isset($userdetails['last_name'])){
              $ls = str_replace("'", "''",$userdetails['last_name']);
             // $sql.= "AND [user].last_name = '$ls' ";
              $model->last_name = $ls;
            }

            if(isset($userdetails['employee_number'])){
              $employee_number = str_replace("'", "''",$userdetails['employee_number']);
              $sql.= "AND [user].employee_number = '$employee_number' ";
              $model->employee_number =  $userdetails['employee_number']; 
            }

            if(isset($userdetails[0]['email'])){
              $email = $userdetails[0]['email'];
              $sql.= "AND [user].email = '$email' ";
              $model->email = $email;
          }
            
        
            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();

            if(!$recordAlreadyExists){ 

            // User
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();
            
              // User-Jobsite Save
            $user_jobsite_status = true;
            if( isset( $userdetails[0]['jobsite_id'] ) )
            {

                
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->user_id = $model->id;
                    $user_jobsite_model->jobsite_id = (int)$userdetails[0]['jobsite_id'];
                    if( !$user_jobsite_model->save() )
                    {
                      $user_jobsite_status = false;
                       // echo "user jobsite error"; exit();
                    }
               
            }


            if($model_status && $user_jobsite_status)
            {
              
                $transaction->commit();
              //notification::notifyNewUserToAdmin($model->id,(int)$userdetails[0]['jobsite_id'] );             

                                
                return "true";
            }else{
             
              return "Error while creating account. Please contact WT Team.";
            }

            }else{
              
              return "You are already registered";
            }
        
    }
protected function renderJSON( $data )
    {
        ob_clean(); // clear output buffer to avoid rendering anything else
        header( 'Content-type: application/json' ); // set content type header as json
        exit( json_encode( $data ) );
    }

 /**
     * Finds the Contractor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contractor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findContractorModel($id)
    {
        if (($model = Contractor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

     /**
     * Web Job Sync TZ WT users Creation.
     * @return mixed
     */
    public function actionWebjobwtusercreation() {

       $userdetails_array = Yii::$app->db->createCommand( "select  TOP 4  firstname, lastname, ad.username as username, email, ce.EMP_NO as employee_no FROM [dbo].[ad_emails] ad 
       inner join [dbo].[CMIC_EMP] ce on ce.EMAIL_ADD = ad.email where ad.status = 0 
       AND ad.username not in (select u.user_name from dbo.[user] u where u.is_active = 1 AND IsAduser = 1);")->queryAll();
       
       if(!empty($userdetails_array)){
        foreach ( $userdetails_array as $userdetails )
        {
             $data = $this->CreateNewTZUser($userdetails);
             echo $data['msg']; 
        }
       }else{
        echo "No new users are created. ". PHP_EOL;
       }
        
       exit('Web job completed');
    }

    protected function CreateNewTZUser($userdetails)
    {
      $transaction = Yii::$app->db->beginTransaction();
      $contractor_id = 148;
      $role_id = 2;
      $model = new User();
      $model->is_active = 1;
      $model->IsAduser = 1;
      $sql = "SELECT * from [user] WHERE ";

      if(isset($contractor_id)){
        $sql.= "[user].contractor_id = $contractor_id ";
        $model->contractor_id = $contractor_id;
      }
     
      if(isset($role_id)){
        $role_id = $role_id;
        $sql.= " AND [user].role_id = $role_id ";
        $model->role_id = $role_id;
      }
 
      if(isset($userdetails['firstname'])){
        $fs = str_replace("'", "''",$userdetails['firstname']);
        $model->first_name = $fs;
      }
      if(isset($userdetails['lastname'])){
        $ls = str_replace("'", "''",$userdetails['lastname']);
        $model->last_name = $ls;
      }

      if(isset($userdetails['username']))
      {
        $user_name=$userdetails['username'];
       $sql.= "AND [user].user_name = '$user_name' ";
        $model->user_name = $user_name;
      }

      if(isset($userdetails['employee_no'])){
        $employee_number = trim(str_replace("'", "''",$userdetails['employee_no']));
        $sql.= "AND [user].employee_number = '$employee_number' ";
        $model->employee_number =  $userdetails['employee_no']; 
      }
      
      if(isset($userdetails['email'])){
        $email = $userdetails['email'];
        $sql.= "AND [user].email = '$email' ";
        $model->email = $email;
    }
    
      $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();
      
            if($recordAlreadyExists){
              $transaction = Yii::$app->db->beginTransaction();
              $model_job=new JobErrors();
              $model_job->message="User already exists";
              $model_job->user_name = $model->user_name;
              $model_status = $model_job->save();
              $transaction->commit();
              $response = array(
                'success' => FALSE,
                'msg' =>  $model->user_name." already exists". PHP_EOL
            );
              return $response;
            }
            else{
            $model_status = $model->save();
            $transaction->commit();
            
            if($model_status)
            {
              $password="";
              notification::notifyNewUser($model->id, $password);
              $transaction = Yii::$app->db->beginTransaction();
              $model_job = new JobErrors();
              $model_job->message = "User created";
              $model_job->user_name = $model->user_name;
              $model_status = $model_job->save();
              $transaction->commit();
              $response = array(
                'success' => TRUE,
                'msg' =>  $model->user_name." has been created successfully". PHP_EOL
            );
              return $response;
            }
            else
            {

              $transaction = Yii::$app->db->beginTransaction();
              $model_joberror = new JobErrors();
              $model_joberror->message = $model->errors["user_name"][0];
              $model_joberror->user_name = $model->user_name;
              $model_joberror->save();
              $transaction->commit();
              $response = array(
                'success' => FALSE,
                'msg' =>  $model->errors["user_name"][0]. PHP_EOL
            );
              return $response;
            }
        }
    }

}
