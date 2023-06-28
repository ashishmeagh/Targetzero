<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resources_type".
 *
 * @property integer $id
 * @property integer $type
 *
 * @property Resources[] $resources
 */
class ResourcesType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resources_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResources()
    {
        return $this->hasMany(Resources::className(), ['type_id' => 'id']);
    }
}
