<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "platform".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $platform
 */
class Platform extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'platform';
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
            [['is_active', 'created', 'updated','platform'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['platform'], 'string', 'max' => 50]
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
            'platform' => 'Platform',
        ];
    }

    
}
