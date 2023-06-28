<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Area as AreaModel;

/**
 * Area represents the model behind the search form about `app\models\Area`.
 */
class Area extends AreaModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'floor_id'], 'integer'],
            [['created', 'updated', 'area'], 'safe'],
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
            $query = AreaModel::find();
            $query->joinWith(['floor', 'floor.building']);
        }
        else
        {
            $query = AreaModel::find()->joinWith("floor")->joinWith("floor.building")->where($where);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['area'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'area',
                    'floor_id' => [
                        'asc' => ['floor' => SORT_ASC],
                        'desc' => ['floor' => SORT_DESC],
                    ],
                    'Building' => [
                        'asc' => ['building' => SORT_ASC],
                        'desc' => ['building' => SORT_DESC],
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
            'area.id' => $this->id,
            'area.is_active' => $this->is_active,
            'area.created' => $this->created,
            'area.updated' => $this->updated,
            'area.floor_id' => $this->floor_id,
        ]);

        $query->andFilterWhere(['like', 'area.area', $this->area]);

        return $dataProvider;
    }
}
