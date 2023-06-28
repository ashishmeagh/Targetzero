<?php 

namespace app\controllers;

use app\components\sqlRoleBuilder;
use app\models\ContractorJobsite;
use app\models\Jobsite;
use Yii;
use app\models\Contractor;
use app\models\UserJobsite;
use app\models\User;
use app\models\searches\Contractor as ContractorSearch;
use app\models\searches\User as UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\searches\ContractorJobsite as ContractorJobsiteSearch;
/**
 * ContractorController implements the CRUD actions for Contractor model.
 */
class ContractorController extends AllController
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
     * Lists all Contractor models.
     * @return mixed
     */
  public function actionIndex()
    {
        $filterByJobsite = '';
        $contractors = '';
        if(Yii::$app->session->get('user.role_id') != ROLE_SYSTEM_ADMIN )
        {
        $filterByJobsite = sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        $contractors = sqlRoleBuilder::getContractorsByJobsiteId($filterByJobsite);
        
        /*if(!(empty(Yii::$app->request->queryParams)) || Yii::$app->request->queryParams["Contractor"]["contractor"] != ' ')
        {        	
			$filterByJobsite = '';
			$contractors = '';
        }*/

        }

              
        $searchModel = new ContractorSearch();
        $dataProvider = $searchModel->searchContractor(Yii::$app->request->queryParams,$contractors);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Contractor model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Contractor();

		// set default active
		$model->is_active = 1;

                // User Post Data
        $postData = Yii::$app->request->post();

        // User Status Post
        $status_load_model = $model->load( $postData );
                
        if ($status_load_model) {

            $sql = "SELECT * from [contractor] WHERE ";
            if(isset($model->contractor)){
              $modelcontractor = str_replace("'", "''",$model->contractor);
              $sql.= "[contractor].contractor = '$modelcontractor'";
            }
            
            $recordAlreadyExists = Yii::$app->db->createCommand($sql)->queryAll();
            if($recordAlreadyExists){
              return $this->redirect(['index', 'id' => $model->id]);
            }
            
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();
            $id = Yii::$app->db->getLastInsertID();

            // Contractor-Jobsite Save
            $contractor_jobsite_status = true;
            if( $model_status && isset( Yii::$app->request->post("Contractor")["jobsites"] ) )
            {
                $added_jobsites = Yii::$app->request->post("Contractor")["jobsites"];
                //si se agregaron jobsites, los agrego
                foreach($added_jobsites as $jobsite_id){
                    $contractor_jobsite_model = new ContractorJobsite();
                    $contractor_jobsite_model->contractor_id = $id;
                    $contractor_jobsite_model->jobsite_id = $jobsite_id;
                    $contractor_jobsite_model->createdby = Yii::$app->session->get('user.full_name') ;
                    if( !$contractor_jobsite_model->save() )
                    {
                        $contractor_jobsite_status = false;
                        break;
                    }
                }

            }
            if($model_status && $contractor_jobsite_status )
            {
                $transaction->commit();
                return $this->redirect(['index', 'id' => $model->id]);
            }
            else
            {
                return $this->render('create', [
                    'model' => $model
                ]);
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Contractor model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $status_load_model = $model->load( Yii::$app->request->post() );

        if ($status_load_model)
        {

            // Contractor
            $transaction = $model->getDb()->beginTransaction();
            $model_status = $model->save();

            // Contractor-Jobsite Save
            $contractor_jobsite_status = true;

            if( isset( Yii::$app->request->post("Contractor")["jobsites"] ) )
            {


                // Delete All Old
                $previous_jobsites_array = array();
                $new_jobsites_array = Yii::$app->request->post("Contractor")["jobsites"];
                 
                $previous_jobsites = ContractorJobsite::find()->where(["contractor_id" => $id])->asArray()->all();
                foreach($previous_jobsites as $jobsite){
                    $previous_jobsites_array[] = $jobsite["jobsite_id"];
                }

                $added_jobsites = array_diff($new_jobsites_array, $previous_jobsites_array);
                $removed_jobsites = array_diff($previous_jobsites_array, $new_jobsites_array);
                    
              $diffJobsites  =  $this->getDifferentJobsiteForUser(Yii::$app->session->get("user.id"),$id);

               if(!empty($diffJobsites))
             {
                     foreach ($diffJobsites as $value) {
                      $anotherJobsite = Jobsite::find()->where(["Id" => $value['Id']])->asArray()->all();
                      array_push($added_jobsites,$value['Id']);
                      }
             }

                 //si se agregaron jobsites, los agrego
                if(!empty($added_jobsites)){
                    foreach($added_jobsites as $jobsite_id){
                        $contractor_jobsite_model = new ContractorJobsite();
                        $contractor_jobsite_model->contractor_id = $id;
                        $contractor_jobsite_model->jobsite_id = $jobsite_id;
                        if( !$contractor_jobsite_model->save() )
                        {
                            $contractor_jobsite_status = false;
                            break;
                        }
                    }
                }

                //si se eliminaron jobsites, los elimino de contractor_jobsite y de los usuarios de dicho contractor
        if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN )
        {
                if(!empty($removed_jobsites)){
                  $array= [];
                    if(!empty($diffJobsites))
                   {
                     foreach ($diffJobsites as $value) {
                     $array[] = $value['Id'];
                    } 
                   }
                    foreach($removed_jobsites as $jobsite_id){
                          if(!in_array($jobsite_id,$array))
                          {
                              //echo "inside remove";
                        ContractorJobsite::deleteAll( [ "contractor_id" => $id ,"jobsite_id" => $jobsite_id] );
                        $users_from_contractor = UserJobsite::find()->joinWith('user')->where([ "[user].contractor_id" => $id, "user_jobsite.jobsite_id" => $jobsite_id])->asArray()->all();
                        foreach($users_from_contractor as $user_jobsite){
                            UserJobsite::deleteAll(["id" => $user_jobsite["id"] ]);  
                          }
                         }
                    }

                }
              
            }

          }
            else{
        if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN )
        {
                ContractorJobsite::deleteAll( [ "contractor_id" => $id ] );
                $users_from_contractor = UserJobsite::find()->joinWith('user')->where([ "[user].contractor_id" => $id ])->asArray()->all();
                foreach($users_from_contractor as $user_jobsite){
                    UserJobsite::deleteAll(["id" => $user_jobsite["id"] ]);
                }
            }

          }

            if($model_status && $contractor_jobsite_status )
            {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
               // return $this->redirect(['index', 'id' => $model->id]);
            }
            else
            {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Deletes an existing Contractor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Displays a single Contractor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
       /* if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN && Yii::$app->session->get( 'user.role_id' ) != ROLE_WT_EXECUTIVE_MANAGER )
        {
            $filterByJobsite = " AND ";
            $filterByJobsite .= sqlRoleBuilder::getJobsiteByUserId( Yii::$app->session->get( 'user.id' ) );
        }
        else
        {*/
            $filterByJobsite = '';
       /* }*/

       /* if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_SYSTEM_ADMIN){

          $IscontractorValid = sqlRoleBuilder::getIsContractorsValid($filterByJobsite, $id);

          if(!$IscontractorValid)
           return $this->redirect(['index']);
        }*/
        $contrJobsitesearchModel = new ContractorJobsiteSearch();
        $contractorDataProvider = $contrJobsitesearchModel->search(Yii::$app->request->queryParams,$id);
        

        $searchModel = new UserSearch();
        $model = new User();
        $userDataProvider = $searchModel->search(Yii::$app->request->queryParams, '[user].contractor_id = '. $id . $filterByJobsite);
        $userDataProvider->pagination->pageSize=7;

        // User Post Data
        $postData = Yii::$app->request->post();
        // User Status Post
        $status_load_model = $model->load( $postData );

        if ($status_load_model)
        {
            // User
            $trasanction = $model->getDb()->beginTransaction();
            $model->contractor_id = $id;
            $model->created = date("Y-m-d H:i:s");
            $model->updated = date("Y-m-d H:i:s");
            $model_status = $model->save();
            // User-Jobsite Save
            $user_jobsite_status = true;
            if( isset( Yii::$app->request->post("User")["jobsites"] ) )
            {
                $jobsites = Yii::$app->request->post("User")["jobsites"];

                for($i=0; $i < count($jobsites); $i++)
                {
                    $user_jobsite_model = new UserJobsite();
                    $user_jobsite_model->user_id = $model->id;
                    $user_jobsite_model->jobsite_id = $jobsites[$i];
                    if( !$user_jobsite_model->save() )
                    {
                        $user_jobsite_status = false;
                        break;
                    }
                }
            }

            if($model_status && $user_jobsite_status)
            {
                $trasanction->commit();
                $model = new User();
                return $this->render('view', [
                    'model' => $this->findModel($id),
                    'contractorDataProvider' => $contractorDataProvider,
                    'searchModel' => $searchModel,
                    'searchModel' => $searchModel,
                    'userModel' => $model,
                    'userDataProvider' => $userDataProvider,
                    'contrJobsitesearchModel' => $contrJobsitesearchModel
                ]);
            }
            else {

                $model = new User();
                return $this->render('view', [
                    'model' => $this->findModel($id),
                    'contractorDataProvider' => $contractorDataProvider,
                    'searchModel' => $searchModel,
                    'userModel' => $model,
                    'userDataProvider' => $userDataProvider,
                    'contrJobsitesearchModel' => $contrJobsitesearchModel
                ]);
            }

        } else {
            return $this->render('view', [
            'model' => $this->findModel($id),
            'contractorDataProvider' => $contractorDataProvider,
            'searchModel' => $searchModel,
            'userModel' => $model,
            'userDataProvider' => $userDataProvider,
            'contrJobsitesearchModel' => $contrJobsitesearchModel
        ]);
        }


    }

    /**
     * Finds the Contractor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contractor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contractor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function getDifferentJobsiteForUser($userId,$contractorId = null)
    {
         if($contractorId != 0)
       {
         $jobsiteByUser = Yii::$app->db->createCommand("select j.Id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id where uj.user_id = $userId")->queryAll();
         $assignedJobsites = Yii::$app->db->createCommand("select distinct j.Id,j.jobsite from contractor c left join [dbo].[contractor_jobsite] cj on c.id = cj.contractor_id left join jobsite j on cj.Jobsite_id = j.id where cj.contractor_id = $contractorId and cj.isdelete = 0 ")->queryAll();
         //$differentvalue =  array_merge(array_diff($jobsiteByUser,$assignedJobsites),array_diff($assignedJobsites,$jobsiteByUser));

         //$jobsitefromdiffuser = array_diff($jobsiteByUser[0],$assignedJobsites[0]);
        // var_dump($jobsitefromdiffuser);
        //$jobsitefromdiffuser =   array_diff_assoc_recursive($jobsiteByUser,$assignedJobsites);
       // print_r($jobsitefromdiffuser);
         $b1 =array();
            foreach($jobsiteByUser as $x)
             $b1[$x['Id']] = $x['jobsite'];

              $b2 =array();
              foreach($assignedJobsites as $x)
              $b2[$x['Id']] = $x['jobsite'];
               
              $c_intersect = array_intersect_key($b1,$b2);
             $c_1 = array_diff_key($b1,$b2);
              $c_2 = array_diff_key($b2,$b1);
              
              $intersect_array = array();
            foreach($c_intersect as $i=>$v)
              $intersect_array[] = array('Id'=>$i,'jobsite'=>$v);

           $only_a1 = array();
             foreach($c_1 as $i=>$v)
                $only_a1[] = array('Id'=>$i,'jobsite'=>$v);

                    $only_a2 = array();
              foreach($c_2 as $i=>$v)
                    $only_a2[] = array('Id'=>$i,'jobsite'=>$v);
           return $only_a2;
       }
    }
    
    public function actionAddJobsite($id){
        
        
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            return $this->renderAjax(['addjobsite', 'id' => $id]);
        }else if(Yii::$app->request->isAjax) {
            
            return $this->renderAjax('addjobsite', [
                        'model' => $model,
                        'id' => $id
            ]);
        }
        //return $this->render('addjobsite');
    }
}
