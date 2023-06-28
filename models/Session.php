<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property integer $user_id
 * @property integer $device_id
 * @property string $token
 *
 * @property Device $device
 * @property User $user
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
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
            [['created', 'updated', 'user_id', 'device_id', 'token'], 'required'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'device_id'], 'integer'],
            [['token'], 'string']
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
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'token' => 'Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
