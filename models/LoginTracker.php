<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "login_tracker".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $timestamp
 * @property string $device
 * @property string $device_id
 * @property string $ip_address
 *
 * @property User $user
 */
class LoginTracker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'login_tracker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'timestamp', 'device', 'ip_address'], 'required'],
            [['user_id'], 'integer'],
            [['timestamp'], 'safe'],
            [['device', 'device_id', 'ip_address'], 'string', 'max' => 255]
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
            'timestamp' => 'Timestamp',
            'device' => 'Device',
            'device_id' => 'Device ID',
            'ip_address' => 'Ip Address',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
