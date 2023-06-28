<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Floor as FloorModel;

/**
 * Floor represents the model behind the search form about `app\models\Floor`.
 */
class Floor extends FloorModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'building_id'], 'integer'],
            [['created', 'updated', 'floor'], 'safe'],
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
            $query = FloorModel::find();
            $query->joinWith(['building', 'building.jobsite']);
        }
        else
        {
            $query = FloorModel::find()->joinWith("building")->joinWith("building.jobsite")->where($where);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['floor'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'floor',
                    'building_id' => [
                        'asc' => ['building' => SORT_ASC],
                        'desc' => ['building' => SORT_DESC],
                    ],
                    'Jobsite' => [
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
            'floor.id' => $this->id,
            'floor.is_active' => $this->is_active,
            'floor.created' => $this->created,
            'floor.updated' => $this->updated,
            'floor.building_id' => $this->building_id,
        ]);

        $query->andFilterWhere(['like', 'floor.floor', $this->floor]);

        return $dataProvider;
    }
}
