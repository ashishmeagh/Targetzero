<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $updated
 * @property string $created
 * @property string $timezone
 * @property string $timezone_code
 *
 * @property Jobsite[] $jobsites
 */
class Timezone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'updated', 'created', 'timezone', 'timezone_code'], 'required'],
            [['is_active'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['timezone'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_active' => 'Is Active',
            'updated' => 'Updated',
            'created' => 'Created',
            'timezone' => 'Timezone',
            'timezone_code' => 'Timezone code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobsites()
    {
        return $this->hasMany(Jobsite::className(), ['timezone_id' => 'id']);
    }
}
