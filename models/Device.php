<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $device
 * @property string $type
 * @property integer $user_id
 *
 * @property User $user
 * @property Session[] $sessions
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        date_default_timezone_set("America/Chicago");
        if(!$this->updated){
            $this->updated = date("Y-m-d H:i:s");
        }
        return [
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['is_active', 'created', 'updated', 'device', 'type', 'user_id'], 'required'],
            [['is_active', 'user_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['device'], 'string'],
            [['type'], 'string', 'max' => 255]
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
            'device' => 'Device',
            'type' => 'Type',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::className(), ['device_id' => 'id']);
    }
}
