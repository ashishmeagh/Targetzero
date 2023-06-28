<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_topic".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $report_topic
 *
 * @property AppCaseIncident[] $appCaseIncidents
 */
class ReportTopic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_topic';
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
            [['is_active', 'created', 'updated', 'report_topic'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['report_topic'], 'string', 'max' => 255]
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
            'report_topic' => 'Report Topic',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseIncidents()
    {
        return $this->hasMany(AppCaseIncident::className(), ['report_topic_id' => 'id']);
    }
}
