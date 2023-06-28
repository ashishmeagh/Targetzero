<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Jobsite as JobsiteModel;

/**
 * Jobsite represents the model behind the search form about `app\models\Jobsite`.
 */
class Jobsite extends JobsiteModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'photo_allowed'], 'integer'],
            [['created', 'updated', 'jobsite'], 'safe'],
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
               $query = JobsiteModel::find(); 
           }
           else {
                   $query = JobsiteModel::find()->where(['jobsite'=>'0000']);
             }
        }
        else
        {
            $query = JobsiteModel::find()->where($where);
        }

        $query->joinWith(['timezone']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['jobsite'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'jobsite',
                    'timezone_id' => [
                        'asc' => ['timezone' => SORT_ASC],
                        'desc' => ['timezone' => SORT_DESC],
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

        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
            'created' => $this->created,
            'updated' => $this->updated,
            'photo_allowed' => $this->photo_allowed
        ]);

        $query->andFilterWhere(['like', 'jobsite', $this->jobsite]);

        return $dataProvider;
    }
}
