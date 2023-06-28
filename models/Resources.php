<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resources".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $creator_id
 * @property integer $type_id
 * @property string $title
 * @property string $description
 * @property string $url
 *
 * @property User $creator
 * @property ResourcesType $type
 */
class Resources extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resources';
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
            [['is_active', 'created', 'updated', 'creator_id', 'type_id', 'title', 'description', 'url'], 'required'],
            [['is_active', 'creator_id', 'type_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['description', 'url'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['url'], 'string', 'min' => 10]
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
            'creator_id' => 'Creator',
            'type_id' => 'Type',
            'title' => 'Title',
            'description' => 'Description',
            'url' => 'URL',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ResourcesType::className(), ['id' => 'type_id']);
    }
}
