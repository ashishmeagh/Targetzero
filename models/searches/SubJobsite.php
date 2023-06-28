<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SubJobsite as SubJobsiteModel;

/**
 * SubJobsite represents the model behind the search form about `app\models\SubJobsite`.
 */
class SubJobsite extends SubJobsiteModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'jobsite_id'], 'integer'],
            [['updated', 'created', 'subjobsite'], 'safe'],
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
    public function search($params,$where=NULL)
    {
        if($where == null)
        {
             if( Yii::$app->session->get( 'user.role_id' ) == ROLE_SYSTEM_ADMIN )
           {
               $query = SubJobsiteModel::find(); 
           }
           else
           {
             $query = SubJobsiteModel::find()->where(['jobsite_id'=>0000]);  
           }   
        }
        else
        {
            $query =SubJobsiteModel::find()->where($where);
        }
        
       // $query = SubJobsiteModel::find();
       //$loggedUserId = Yii::$app->session->get( "user.id" );
       $query->joinWith(['jobsite']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['subjobsite'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'subjobsite',
                    'jobsite_id' => [
                        'asc' => ['jobsite' => SORT_ASC],
                        'desc' => ['jobsite' => SORT_DESC],
                    ],
                    'created',
                    'updated'
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
       
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
            'updated' => $this->updated,
            'created' => $this->created,
            'jobsite_id' => $this->jobsite_id,
        ]);

        $query->andFilterWhere(['like', 'subjobsite', $this->subjobsite]);
      
        return $dataProvider;
    }
}
