<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "building".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $updated
 * @property string $created
 * @property string $building
 * @property resource $description
 * @property string $location
 * @property integer $jobsite_id
 *
 * @property AppCase[] $appCases
 * @property Jobsite $jobsite
 * @property Floor[] $floors
 */
class Building extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building';
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
            [['is_active', 'updated', 'created', 'building', 'jobsite_id'], 'required'],
            [['is_active', 'jobsite_id'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['description'], 'string'],
            [['building', 'location'], 'string', 'max' => 255]
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
            'building' => 'Building',
            'description' => 'Description',
            'location' => 'Location',
            'jobsite_id' => 'Jobsite',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['building_id' => 'id']);
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
    public function getFloors()
    {
        return $this->hasMany(Floor::className(), ['building_id' => 'id']);
    }
}
