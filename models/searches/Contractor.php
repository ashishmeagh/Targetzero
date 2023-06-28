<?php

namespace app\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contractor as ContractorModel;

/**
 * Contractor represents the model behind the search form about `app\models\Contractor`.
 */
class Contractor extends ContractorModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['updated', 'created', 'contractor', 'address'], 'safe'],
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
     public function search($params, $contractors)
    {

       $query = ContractorModel::find()
                  ->where($contractors);
      
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=> SORT_DESC],
                'attributes' => [
                    'is_active',
                    'contractor',
                    'created',
                    'vendor_number',
                    'updated',
                    'cmic_updated'
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
        ]);
       //$query->andWhere(['not', ['cmic_updated' => null]]);
        $query->andFilterWhere(['or', ['like', 'vendor_number', $this->contractor], ['like', 'contractor', $this->contractor],]);;

        return $dataProvider;
    }

        /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
     public function searchContractor($params, $contractors)
    {

        $contractorparams = '';
        $filterwhere = true;
      


        if ((sizeof($params) > 0) && (isset($params["page"]))) {

           $filterwhere = false;
        } 

        if ((sizeof($params)  > 0) && (isset($params["sort"]))) {

           $filterwhere = false;
        } 

      if(isset($params["Contractor"]["contractor"])){
        $contractorparams = $params["Contractor"]["contractor"];
      }

       if ($contractorparams == ''){
       
        $query = ContractorModel::find()
                  ->where($contractors);        
      
       }else{
        
        $query = ContractorModel::find();
      
       }
       
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created'=> SORT_DESC],
                'attributes' => [
                    'is_active',
                    'contractor',
                    'created',
                    'vendor_number',
                    'updated',
                    'cmic_updated'
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
            'is_cmic' => 1,
        ]);
       $query->andWhere(['not', ['cmic_updated' => null]]);


       if(!empty($params) && $contractorparams != ''){          
        $query->andWhere(['or', ['like', 'vendor_number', $this->contractor], ['like', 'contractor', $this->contractor],]);
        }
       
        return $dataProvider;
    }
}
