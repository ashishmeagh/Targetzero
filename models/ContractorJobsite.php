<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contractor_jobsite".
 *
 * @property integer $id
 * @property integer $contractor_id
 * @property integer $jobsite_id
 *
 * @property Jobsite $jobsite
 * @property Contractor $contractor
 */
class ContractorJobsite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contractor_jobsite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contractor_id', 'jobsite_id'], 'required'],
            [['contractor_id', 'jobsite_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contractor_id' => 'Contractor ID',
            'jobsite_id' => 'Jobsite ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobsite()
    {
        return $this->hasOne(Jobsite::className(), ['id' => 'jobsite_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Contractor::className(), ['id' => 'contractor_id']);
    }

    /**
     * Trae los contractors relacionados con n jobsites
     * @params mix $array_jobsites Array de jobsites
     * @return \yii\db\ActiveQuery
     */
    public static function getContractorsForJobsites($array_jobsites){
        if($array_jobsites){
            $jobsites = array();
            foreach($array_jobsites as $jobsite_id => $jobsite){
                $jobsites[] = $jobsite_id;
            }
            $jobsites = "( " . implode( ", ", $jobsites) . " )";
            $query = "SELECT * FROM contractor c JOIN contractor_jobsite cj ON cj.contractor_id = c.id WHERE c.is_active = 1 AND cj.jobsite_id IN $jobsites ORDER BY c.contractor ASC" ;
            $array_contractors = Yii::$app->db->createCommand( "SELECT * FROM contractor c JOIN contractor_jobsite cj ON cj.contractor_id = c.id WHERE c.is_active = 1 AND cj.jobsite_id IN $jobsites ORDER BY c.contractor ASC" )->queryAll();

            $contractors = array();
            foreach($array_contractors as $contractor){
                $contractor_id = $contractor["contractor_id"];
                $contractors["$contractor_id"] = $contractor["contractor"];
            }
        }else{
            $contractors = array("" => "-");
        }
        return $contractors;
    }

        /**
     * This to get the contractors for logged in User
     * @params Logged in User ID
     * @return \yii\db\ActiveQuery
     */
    public function getContractorsForLoggedInUser($loggedInUserId){

        if($loggedInUserId){        
               $array_contractors = Yii::$app->db->createCommand( "SELECT * FROM contractor c  join [user] u on u.contractor_id =c.id WHERE c.is_active = 1  And u.id = $loggedInUserId ORDER BY c.contractor ASC" )->queryAll();

            $contractors = array();
            foreach($array_contractors as $contractor){
                $contractor_id = $contractor["contractor_id"];
                $contractors["$contractor_id"] = $contractor["contractor"];
            }
        }else{
            $contractors = array("" => "-");
        }
        return $contractors;
    }
    
    
    public  function getContractorForJobsite()
    {
        return $this->hasMany(ContractorJobsite::className(), ['jobsite_id' => 'id']); 
    }
}
