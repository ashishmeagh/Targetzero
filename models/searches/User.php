<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User as UserModel;
use app\components\sqlRoleBuilder;
/**
 * User represents the model behind the search form about `app\models\User`.
 */
class User extends UserModel
{

	public $all_search;	
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'role_id', 'contractor_id'], 'integer'],
            //[['created', 'updated', 'user_name', 'first_name', 'last_name', 'email', 'phone', 'division', 'employee_number', 'password'], 'safe'],
			[['all_search'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchUser($params,$where=NULL)
    {
        if($where == null)
        {
             if( Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN )
           {
               $query = UserModel::find(); 
           }
           else
           {
            $query = UserModel::find()->where(['first_name'=>'00000yu0']); 
           }
            
        }
        else
        {

         $value =  sqlRoleBuilder::getUsersByJobsites($where);

         $query = UserModel::find()->where($value . ' and contractor_id ='.Yii::$app->session->get( 'user.contractor_id' ));         

          //$query = UserModel::find()->joinWith('userJobsites')->where($where);
        }

        $query->joinWith(['role', 'contractor']);
  
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=>SORT_DESC],
                'attributes' => [
                    'is_active',
                    'employee_number',
                    'first_name' => [
                        'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                        'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                        'label' => 'Full Name',
                        'default' => SORT_ASC
                    ],
//                    'first_name' => [
//                        'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
//                        'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
//                    ],
                    'role_id' => [
                        'asc' => ['role.role' => SORT_ASC],
                        'desc' => ['role.role' => SORT_DESC],
                    ],
                    'contractor_id' => [
                        'asc' => ['contractor.contractor' => SORT_ASC],
                        'desc' => ['contractor.contractor' => SORT_DESC],
                    ],
                    'created',
                    'updated'
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		$query->joinWith('role');
		$query->joinWith('contractor');

        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.is_active' => $this->is_active,
            'user.created' => $this->created,
            'user.updated' => $this->updated,
            'user.role_id' => $this->role_id,
            'user.contractor_id' => $this->contractor_id,
        ]);
        $query->andFilterWhere(['like', 'user.first_name', $this->all_search])
            ->orFilterWhere(['like', 'user.last_name', $this->all_search])
            ->orFilterWhere(['like',"CONCAT([first_name], ' ', [last_name])", $this->all_search])
//            ->orFilterWhere(['like', 'user.email', $this->all_search])
//            ->orFilterWhere(['like', 'user.phone', $this->all_search])
            ->orFilterWhere(['like', 'role.role', $this->all_search])
            ->orFilterWhere(['like', 'contractor.contractor', $this->all_search])
            ->orFilterWhere(['like', 'user.employee_number', $this->all_search])
            ->orFilterWhere([ 'user.contractor_id' => $this->contractor_id]);

//        $query->andFilterWhere(['like', 'email', $this->all_search])
//            ->orFilterWhere(['like', 'phone', $this->all_search])
//            ->orFilterWhere(['like', 'role.role', $this->all_search])
//            ->orFilterWhere(['like', 'contractor.contractor', $this->all_search])
//            ->orFilterWhere(['like', 'employee_number', $this->all_search])
//            ->orFilterWhere([ 'contractor_id' => $this->contractor_id]);

//        $query->andWhere('first_name LIKE "%' . $this->fullName . '%" ' .
//            'OR last_name LIKE "%' . $this->fullName . '%"'
//        );
//        $query->orderBy('employee_number asc,  is_active');


        return $dataProvider;
    }
    
    
      public function search($params,$where=NULL,$displayinactiveusers = false, $GetJobsiteContractors=NULL,$IsJobsiteUserSearch=false)
    {

        if((isset($params["User"]["all_search"]))&&($IsJobsiteUserSearch==false)){            
            $where=NULL;  
        }

        if($where==null)
        {
            $query = UserModel::find();
        }
        else        {
            $query = UserModel::find()->joinWith('userJobsites')->where($where);
        }

        $query->joinWith(['role', 'contractor']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=>SORT_DESC],
                'attributes' => [
                    'is_active',
                    'employee_number',
                    'first_name' => [
                        'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                        'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                        'label' => 'Full Name',
                        'default' => SORT_ASC
                    ],
//                    'first_name' => [
//                        'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
//                        'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
//                    ],
                    'role_id' => [
                        'asc' => ['role.role' => SORT_ASC],
                        'desc' => ['role.role' => SORT_DESC],
                    ],
                    'contractor_id' => [
                        'asc' => ['contractor.contractor' => SORT_ASC],
                        'desc' => ['contractor.contractor' => SORT_DESC],
                    ],
                    'created',
                    'updated'
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		$query->joinWith('role');
		$query->joinWith('contractor');

        if(!$displayinactiveusers){
           $this->is_active = 1;
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.is_active' => $this->is_active,
            'user.created' => $this->created,
            'user.updated' => $this->updated,
            'user.role_id' => $this->role_id,
            'user.contractor_id' => $this->contractor_id,
        ]);
 
         if($this->all_search != ''){
             $query->andWhere(['or', ['like', 'user.first_name' , $this->all_search],['like','user.last_name', $this->all_search], ['like',"CONCAT(Ltrim(Rtrim([first_name])), ' ', Ltrim(Rtrim([last_name])))" , $this->all_search], ['like', 'role.role' , $this->all_search], ['like','contractor.contractor' , $this->all_search], ['like','user.user_name' , $this->all_search], ['like', 'user.employee_number' , $this->all_search]]); 
            if($GetJobsiteContractors != null){
                $GetJobsiteContractors_array = explode(",",$GetJobsiteContractors);
                  $query->andWhere(['and', ['in', 'user.contractor_id', $GetJobsiteContractors_array]]); 
            }
           
         } 
//$query->andWhere(['and', ['user.contractor_id' => $this->contractor_id]]); 
        
       /*$query->andFilterWhere(['like', 'user.first_name', $this->all_search])
            ->orFilterWhere(['like', 'user.last_name', $this->all_search])
            ->orFilterWhere(['like',"CONCAT([first_name], ' ', [last_name])", $this->all_search])
//            ->orFilterWhere(['like', 'user.email', $this->all_search])
//            ->orFilterWhere(['like', 'user.phone', $this->all_search])
            ->orFilterWhere(['like', 'role.role', $this->all_search])
            ->orFilterWhere(['like', 'contractor.contractor', $this->all_search])
            ->orFilterWhere(['like', 'user.employee_number', $this->all_search])
            ->orFilterWhere([ 'user.contractor_id' => $this->contractor_id]);*/

//        $query->andFilterWhere(['like', 'email', $this->all_search])
//            ->orFilterWhere(['like', 'phone', $this->all_search])
//            ->orFilterWhere(['like', 'role.role', $this->all_search])
//            ->orFilterWhere(['like', 'contractor.contractor', $this->all_search])
//            ->orFilterWhere(['like', 'employee_number', $this->all_search])
//            ->orFilterWhere([ 'contractor_id' => $this->contractor_id]);

//        $query->andWhere('first_name LIKE "%' . $this->fullName . '%" ' .
//            'OR last_name LIKE "%' . $this->fullName . '%"'
//        );
//        $query->orderBy('employee_number asc,  is_active');
        return $dataProvider;
    }
}
