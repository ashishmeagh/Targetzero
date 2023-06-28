<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "changes_tracker".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $timestamp
 * @property integer $model_id
 * @property string $model_name
 * @property string $field_name
 * @property string $before_state
 * @property string $after_state
 *
 * @property User $user
 */
class ChangesTracker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'changes_tracker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'timestamp', 'model_id', 'model_name', 'field_name', 'before_state', 'after_state'], 'required'],
            [['user_id', 'model_id'], 'integer'],
            [['timestamp'], 'safe'],
            [['model_name', 'field_name', 'before_state', 'after_state'], 'string', 'max' => 255]
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
            'model_id' => 'Model ID',
            'model_name' => 'Model Name',
            'field_name' => 'Field Name',
            'before_state' => 'Before State',
            'after_state' => 'After State',
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
