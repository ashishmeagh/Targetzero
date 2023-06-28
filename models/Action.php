<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "action".
 *
 * @property integer $id
 * @property string $action
 *
 * @property Permission[] $permissions
 */
class Action extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['action'], 'string', 'max' => 70]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Action',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['action_id' => 'id']);
    }
}
