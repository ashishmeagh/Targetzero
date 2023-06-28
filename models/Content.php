<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content".
 *
 * @property integer $id
 * @property integer $is_active
 * @property integer $uploader_id
 * @property string $updated
 * @property string $created
 * @property integer $app_case_id
 * @property string $type
 * @property string $file
 *
 * @property AppCase $appCase
 */
class Content extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $this->updated = date("Y-m-d H:i:s");
        return [
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['is_active', 'updated', 'created', 'app_case_id', 'file'], 'required'],
            [['is_active', 'app_case_id', 'uploader_id'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['type'], 'string', 'max' => 75],
            [['file'], 'string', 'max' => 255]
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
            'updated' => 'Updated',
            'created' => 'Created',
            'app_case_id' => 'App Case ID',
            'type' => 'Type',
            'file' => 'File',
            'uploader_id' => 'Uploader',
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
        return $this->hasOne(User::className(), ['id' => 'uploader_id']);
    }
}
