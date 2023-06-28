<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_jobsite".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $jobsite_id
 * @property integer $is_admin
 *
 * @property Jobsite $jobsite
 * @property User $user
 */
class UserJobsite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_jobsite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'jobsite_id'], 'required'],
            [['user_id', 'jobsite_id', 'is_admin'], 'integer']
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
            'jobsite_id' => 'Jobsite ID',
        ];
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
