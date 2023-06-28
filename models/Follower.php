<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "follower".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $app_case_id
 *
 * @property AppCase $appCase
 * @property User $user
 */
class Follower extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follower';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'app_case_id'], 'required'],
            [['user_id', 'app_case_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'app_case_id' => 'App Case ID',
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
