<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AppCaseSfCode as AppCaseSfCodeModel;

/**
 * AppCaseSfCode represents the model behind the search form about `app\models\AppCaseSfCode`.
 */
class AppCaseSfCode extends AppCaseSfCodeModel
{
	public $all_search;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['id', 'is_active', 'parent_id'], 'integer'],
            //[['created', 'updated', 'code', 'description'], 'safe'],
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
    public function search($params)
    {
        $query = AppCaseSfCodeModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['code'=>SORT_ASC],
                'attributes' => [
                    'is_active',
                    'code',
                    'parent_id',
                    'description',
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
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->all_search])
            ->orFilterWhere(['like', 'description', $this->all_search]);

        return $dataProvider;
    }
}
