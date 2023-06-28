<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contractor".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $updated
 * @property string $created
 * @property string $contractor
 * @property string $vendor_number
 * @property string $address
 *
 * @property AppCase[] $appCases
 * @property User[] $users
 */
class Contractor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contractor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        date_default_timezone_set("America/Chicago");
        $this->updated = date("Y-m-d H:i:s");
        return [
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['vendor_number'], 'default', 'value' => null ],
            [['is_active', 'updated', 'created', 'contractor'], 'required'],
            [['is_active'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['contractor', 'address', 'vendor_number'], 'string', 'max' => 255],
            [['vendor_number'], 'unique', 'filter' => ['=','is_cmic' , '1']]
        ];
    }
 
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created',
            'contractor' => 'Contractor',
            'address' => 'Address',
            'vendor_number' => 'Vendor number',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['contractor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['contractor_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractorJobsites()
    {
        return $this->hasMany(ContractorJobsite::className(), ['contractor_id' => 'id']);
    }

}
