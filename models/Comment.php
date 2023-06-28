<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $app_case_id
 * @property integer $user_id
 * @property string $comment
 * @property integer $report_type_id
 *
 * @property AppCase $appCase
 * @property ReportType $reportType
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
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
            [['comment','recordable','is_property_damage','is_lost_time'], 'required'],
			[['report_type_id'], 'default', 'value' => null ],
            [['is_active', 'app_case_id', 'user_id', 'report_type_id','causation_factor','recordable','is_property_damage','is_lost_time'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['comment'], 'string'],
            ['causation_factor','required',
                'when' => function($model_detail){ 
                           return $model_detail->report_type_id == '3'; //show error message when report type is 'final'
               },
                'whenClient' => "function (attribute, value) { 
                  return $('#report_type_id').val() == '3'; 
              }"      
           ],
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
            'app_case_id' => 'App Case ID',
            'user_id' => 'User ID',
            'comment' => 'Comment',
            'report_type_id' => 'Report Type',
            'recordable' => 'Recordable',
            'lost_time' => 'Lost Time',
            'is_property_damage' => 'Property Damage',
            
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
    public function getReportType()
    {
        return $this->hasOne(ReportType::className(), ['id' => 'report_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
