<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "floor".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $building_id
 * @property string $floor
 *
 * @property AppCase[] $appCases
 * @property Area[] $areas
 * @property Building $building
 */
class Floor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'floor';
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
            [['is_active', 'created', 'updated', 'building_id','floor'], 'required'],
            [['is_active', 'building_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['floor'], 'string', 'max' => 255]
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
            'created' => 'Created',
            'updated' => 'Updated',
            'building_id' => 'Building',
            'floor' => 'Floor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['floor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::className(), ['floor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * Trae el jobsite correspondiente a un building
     * @params int $building_id ID de un building
     * @return \yii\db\ActiveQuery
     */
	public function getJobsite($building_id)
	{
		//get jobsite ID
		$jobsite_id = Building::find()->select('jobsite_id')->where(['id' => $building_id])->asArray()->all();
		//get jobsite NAME
		$jobsite_name = Jobsite::find()->select('jobsite')->where(['id' => $jobsite_id[0]['jobsite_id']])->asArray()->all();
		return $jobsite_name[0]['jobsite'];
	}
}
