<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "body_part".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $body_part
 *
 * @property AppCaseIncident[] $appCaseIncidents
 */
class BodyPart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'body_part';
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
            [['is_active', 'created', 'updated', 'body_part'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['body_part'], 'string', 'max' => 255]
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
            'body_part' => 'Body Part',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseIncidents()
    {
        return $this->hasMany(AppCaseIncident::className(), ['body_part_id' => 'id']);
    }
}
