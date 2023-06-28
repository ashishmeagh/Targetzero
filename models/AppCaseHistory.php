<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_case_history".
 *
 * @property integer $id
 * @property string $created
 * @property integer $creator_id
 * @property integer $app_case_id
 * @property string $log
 *
 * @property AppCase $appCase
 * @property User $creator
 * @property Notification[] $notifications
 */
class AppCaseHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_case_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['created', 'creator_id', 'app_case_id', 'log'], 'required'],
            [['created'], 'safe'],
            [['creator_id', 'app_case_id'], 'integer'],
            [['log'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'creator_id' => 'Creator ID',
            'app_case_id' => 'App Case ID',
            'log' => 'Log',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCase()
    {
        return $this->hasOne(AppCase::className(), ['id' => 'app_case_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['app_case_history_id' => 'id']);
    }
}
