<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AppCase as AppCaseModel;

/**
 * AppCase represents the model behind the search form about `app\models\AppCase`.
 */
class AppCase extends AppCaseModel
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['is_active', 'app_case_status_id', 'app_case_type_id'], 'integer'],
            [['additional_information', 'creator_id', 'contractor_id', 'jobsite_id', 'app_case_type_id'], 'safe'],
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
    public function search($params,$where=NULL,$otherissues=false)
    {        
      
	if($where == null)
        {
           if( (Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN ) && $otherissues == false)
           {
                 $query = AppCase::find()
                           ->leftJoin('app_case_Incident', 'app_case_Incident.app_case_id = app_case.id')
                           ->leftJoin('app_case_observation', 'app_case_observation.app_case_id = app_case.id')
                           ->leftJoin('app_case_recognition', 'app_case_recognition.app_case_id = app_case.id')
                           ->leftJoin('app_case_violation', 'app_case_violation.app_case_id = app_case.id')
                           ->andWhere(['or',['is not','app_case_Incident.id', null],['is not','app_case_observation.id', null],['is not','app_case_recognition.id', null],['is not','app_case_violation.id', null]]);  
                         
            
           }
           else
           {
               $query = AppCase::find()
                           ->leftJoin('app_case_Incident', 'app_case_Incident.app_case_id = app_case.id')
                           ->leftJoin('app_case_observation', 'app_case_observation.app_case_id = app_case.id')
                           ->leftJoin('app_case_recognition', 'app_case_recognition.app_case_id = app_case.id')
                           ->leftJoin('app_case_violation', 'app_case_violation.app_case_id = app_case.id')
                           ->andWhere(['jobsite_id'=>0000])
                           ->andWhere(['or',['is not','app_case_Incident.id', null],['is not','app_case_observation.id', null],['is not','app_case_recognition.id', null],['is not','app_case_violation.id', null]]);  
            
           } 
        }
        else
        {
            
              $query = AppCase::find()
                       ->leftJoin('app_case_Incident', 'app_case_Incident.app_case_id = app_case.id')
                       ->leftJoin('app_case_observation', 'app_case_observation.app_case_id = app_case.id')
                       ->leftJoin('app_case_recognition', 'app_case_recognition.app_case_id = app_case.id')
                       ->leftJoin('app_case_violation', 'app_case_violation.app_case_id = app_case.id')
                       ->andWhere(['or',['is not','app_case_Incident.id', null],['is not','app_case_observation.id', null],['is not','app_case_recognition.id', null],['is not','app_case_violation.id', null]])
                       ->andwhere($where); 
            
        }
        $query->joinWith(['appCaseStatus', 'creator', 'contractor', 'jobsite', 'appCaseType','affectedUser' ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=>SORT_DESC],
                'attributes' => [
                    'additional_information',
                    'app_case_status_id' => [
                        'asc' => ['app_case_status.status' => SORT_ASC],
                        'desc' => ['app_case_status.status' => SORT_DESC],
                    ],
                    'creator_id' => [
                        'asc' => ['trim([user].first_name)' => SORT_ASC, 'trim([user].last_name)' => SORT_ASC],
                        'desc' => ['trim([user].first_name)' => SORT_DESC,'trim([user].last_name)' => SORT_DESC], 
                    ],
                    'contractor_id' => [
                        'asc' => ['contractor.contractor' => SORT_ASC],
                        'desc' => ['contractor.contractor' => SORT_DESC],
                    ],
                    'affected_user_id' => [
                        'asc' => ['trim([affecteduser].first_name)' => SORT_ASC,'trim([affecteduser].last_name)' => SORT_ASC],
                        'desc' => ['trim([affecteduser].first_name)' => SORT_DESC,'trim([affecteduser].last_name)' => SORT_DESC], 
                    ],
                    'jobsite_id' => [
                        'asc' => ['jobsite.jobsite' => SORT_ASC],
                        'desc' => ['jobsite.jobsite' => SORT_DESC],
                    ],
                    'app_case_type_id' => [
                        'asc' => ['app_case_type.type' => SORT_ASC],
                        'desc' => ['app_case_type.type' => SORT_DESC],
                    ],
                    'created',
                    'updated'
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

//       to do lo comentado hacia abajo estaba para el search por palabras, pero se opto por el search por dropdown

//		$query->joinWith('creator');
//		$query->joinWith('contractor');
//		$query->joinWith('jobsite');

        $query->andFilterWhere([
            'is_active' => $this->is_active,
            'app_case_type_id' => $this->app_case_type_id,
            'app_case_status_id' => $this->app_case_status_id,
            'creator_id' => $this->creator_id,
            'app_case.contractor_id' => $this->contractor_id,
            'jobsite_id' => $this->jobsite_id
        ]);
        $query->andFilterWhere(['like', 'additional_information', $this->additional_information]);
//			->andFilterWhere(['like', 'user.first_name', $this->creator_id])
//			->orFilterWhere(['like', 'user.last_name', $this->creator_id])
//			->andFilterWhere(['like', 'contractor.contractor', $this->contractor_id])
//			->andFilterWhere(['like', 'jobsite.jobsite', $this->jobsite_id]);

//        $query->orderBy('created DESC');

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function GetRepeatoffenderissues($params,$afid,$jid)
    {        
      
                      
       $query = AppCase::find()
                         ->where(['affected_user_id'=>$afid, 'jobsite_id'=>$jid])
                          ->andWhere(['<>','app_case_type_id', 2])      ;       
       
        
        $query->joinWith(['appCaseStatus', 'creator', 'contractor', 'jobsite', 'appCaseType']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=>SORT_DESC],
                'attributes' => [
                    'additional_information',
                    'app_case_status_id' => [
                        'asc' => ['app_case_status.status' => SORT_ASC],
                        'desc' => ['app_case_status.status' => SORT_DESC],
                    ],
                    'creator_id' => [
                        'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                        'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                    ],
                    'contractor_id' => [
                        'asc' => ['contractor.contractor' => SORT_ASC],
                        'desc' => ['contractor.contractor' => SORT_DESC],
                    ],
                    'jobsite_id' => [
                        'asc' => ['jobsite.jobsite' => SORT_ASC],
                        'desc' => ['jobsite.jobsite' => SORT_DESC],
                    ],
                    'app_case_type_id' => [
                        'asc' => ['app_case_type.type' => SORT_ASC],
                        'desc' => ['app_case_type.type' => SORT_DESC],
                    ],
                    'created',
                    'updated'
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

//       to do lo comentado hacia abajo estaba para el search por palabras, pero se opto por el search por dropdown

//      $query->joinWith('creator');
//      $query->joinWith('contractor');
//      $query->joinWith('jobsite');

        $query->andFilterWhere([
            'is_active' => $this->is_active,
            'app_case_type_id' => $this->app_case_type_id,
            'app_case_status_id' => $this->app_case_status_id,
            'creator_id' => $this->creator_id,
            'app_case.contractor_id' => $this->contractor_id,
            'jobsite_id' => $this->jobsite_id
        ]);
        $query->andFilterWhere(['like', 'additional_information', $this->additional_information]);
//          ->andFilterWhere(['like', 'user.first_name', $this->creator_id])
//          ->orFilterWhere(['like', 'user.last_name', $this->creator_id])
//          ->andFilterWhere(['like', 'contractor.contractor', $this->contractor_id])
//          ->andFilterWhere(['like', 'jobsite.jobsite', $this->jobsite_id]);

//        $query->orderBy('created DESC');

        return $dataProvider;
    }

    /**
     * TO check the Repeat offender
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function CheckRepeatoffenderissues($affected_user_id,$jobsite_id)
    {  
        $offenderuserandissues = Yii::$app->db->createCommand("SELECT a.id,Upper(ac.type) as issue_type "
            . "FROM [app_case] as a "
            . "JOIN [user] u on u.id =a.affected_user_id "
            . "JOIN app_case_type ac on ac.id =a.app_case_type_id "
            . "WHERE a.affected_user_id = $affected_user_id "
            . "AND a.app_case_type_id <> 2 AND a.jobsite_id =$jobsite_id"
            . "order by a.created")->queryAll();

        return $offenderuserandissues;

    }
}
