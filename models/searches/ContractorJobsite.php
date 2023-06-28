<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ContractorJobsite as ContractorJobsiteModel;

/**
 * Contractor represents the model behind the search form about `app\models\Contractor`.
 */
class ContractorJobsite extends ContractorJobsiteModel
{
    public $jobsite;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contractor_id', 'jobsite_id'], 'integer'],
            [['jobsite'], 'safe'],
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
     public function search($params, $cid)
    {
        $loggedinuserid = Yii::$app->session->get( 'user.id');
        if($loggedinuserid != ''){        
        $query = Jobsite::find()
                 ->select('[jobsite].id,  [jobsite].jobsite')
                 ->innerjoinWith('contractorJobsites')
                ->where([ "[jobsite].is_active" => 1, "contractor_id" => $cid ])
                ->groupBy(['[jobsite].id', '[jobsite].jobsite'])
                ->orderBy(['jobsite.jobsite'=>SORT_ASC]); 
                               

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> [
                'attributes' => [
                   'user_id',                    
                    'jobsite'
                ]
            ]
        ]);

         $this->load($params);

        if (!$this->validate()) {            
            return $dataProvider;
        }

        if(isset($params["ContractorJobsite"]["jobsite"])){
            $jobsite = $params["ContractorJobsite"]["jobsite"];
            $query->andFilterWhere(['like', 'jobsite', $jobsite]);
        }
        

        return $dataProvider;
    }else{
        return false;
    }

    }
}
