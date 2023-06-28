<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sub_jobsite".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $updated
 * @property string $created
 * @property string $subjobsite
 * @property integer $jobsite_id
 *
 * @property Jobsite $jobsite
 */
class SubJobsite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sub_jobsite';
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
            [['is_active', 'updated', 'created', 'subjobsite', 'jobsite_id','subjob_number'], 'required'],
            [['is_active', 'jobsite_id'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['subjobsite'], 'string', 'max' => 255],
            [['subjob_number'], 'string', 'max' => 15],
            [['subjob_number'], 'match', 'pattern' => '/^[a-zA-Z0-9.]*$/', 'message' => 'Invalid characters in Subjob Number.Only alphabets, numbers and period is allowed.']
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
            'subjobsite' => 'Sub jobsite',
            'jobsite_id' => 'Jobsite',
            'subjob_number' => 'Subjob Number'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobsite()
    {
        return $this->hasOne(Jobsite::className(), ['id' => 'jobsite_id']);
    }
}
