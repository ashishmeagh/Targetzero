<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trade".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $trade
 *
 * @property AppCase[] $appCases
 */
class Trade extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trade';
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
            [['is_active', 'created', 'updated', 'trade'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['trade'], 'string', 'max' => 255]
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
            'trade' => 'Trade',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['trade_id' => 'id']);
    }
}
