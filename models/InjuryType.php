<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "injury_type".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $injury_type
 *
 * @property AppCaseIncident[] $appCaseIncidents
 */
class InjuryType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'injury_type';
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
            [['is_active', 'created', 'updated', 'injury_type'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['injury_type'], 'string', 'max' => 255]
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
            'injury_type' => 'Injury Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseIncidents()
    {
        return $this->hasMany(AppCaseIncident::className(), ['injury_type_id' => 'id']);
    }
}
