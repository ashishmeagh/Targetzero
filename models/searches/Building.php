<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Building as BuildingModel;

/**
 * Building represents the model behind the search form about `app\models\Building`.
 */
class Building extends BuildingModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'jobsite_id'], 'integer'],
            [['updated', 'created', 'building', 'description', 'location'], 'safe'],
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
            $query = BuildingModel::find();
        }
        else
        {
            $query = BuildingModel::find()->where($where);
        }

        $query->joinWith(['jobsite']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['building'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'building',
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
            // uncomment the following line if you do not want to any records when validation fails
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

        $query->andFilterWhere(['like', 'building', $this->building])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }
}
