<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_case_status".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $status
 *
 * @property AppCase[] $appCases
 */
class AppCaseStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_case_status';
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
            [['is_active', 'created', 'updated', 'status'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['status'], 'string', 'max' => 255]
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
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['app_case_status_id' => 'id']);
    }
}
