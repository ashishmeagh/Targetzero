<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property integer $app_case_id
 * @property integer $user_id
 * @property integer $app_case_history_id
 *
 * @property AppCaseHistory $appCaseHistory
 * @property AppCase $appCase
 * @property User $user
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $this->updated = date("Y-m-d H:i:s");
        return [
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['created', 'updated', 'app_case_id', 'user_id', 'app_case_history_id'], 'required'],
            [['created', 'updated'], 'safe'],
            [['app_case_id', 'user_id', 'app_case_history_id'], 'integer']
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
            'updated' => 'Updated',
            'app_case_id' => 'App Case ID',
            'user_id' => 'User ID',
            'app_case_history_id' => 'App Case History ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseHistory()
    {
        return $this->hasOne(AppCaseHistory::className(), ['id' => 'app_case_history_id']);
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
