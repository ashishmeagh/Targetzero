<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_case_observation".
 *
 * @property integer $id
 * @property integer $app_case_id
 * @property integer $foreman_id
 * @property string $correction_date
 * @property string $coaching_provider
 *
 * @property AppCase $appCase
 * @property User $foreman
 */
class AppCaseObservation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_case_observation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_case_id', 'correction_date'], 'required'],
            [['app_case_id', 'foreman_id'], 'integer'],
            [['correction_date'], 'safe'],
            [['coaching_provider'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_case_id' => 'App Case ID',
            'foreman_id' => 'Foreman',
            'correction_date' => 'Observation Date',
            'coaching_provider' => 'Coaching Provided',
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
    public function getForeman()
    {
        return $this->hasOne(User::className(), ['id' => 'foreman_id']);
    }
}
