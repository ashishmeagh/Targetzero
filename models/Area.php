<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $floor_id
 * @property string $area
 *
 * @property AppCase[] $appCases
 * @property Floor $floor
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
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
            [['is_active', 'created', 'updated', 'floor_id', 'area'], 'required'],
            [['is_active', 'floor_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['area'], 'string', 'max' => 255]
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
            'floor_id' => 'Floor',
            'area' => 'Area',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFloor()
    {
        return $this->hasOne(Floor::className(), ['id' => 'floor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getBuilding($floor_id)
	{
		//get building ID
		$building_id = Floor::find()->select('building_id')->where(['id' => $floor_id])->asArray()->all();
		//get building NAME
		$building_name = Building::find()->select('building')->where(['id' => $building_id[0]['building_id']])->asArray()->all();
		return $building_name[0]['building'];
	}
}
