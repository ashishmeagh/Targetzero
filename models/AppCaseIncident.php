<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_case_incident".
 *
 * @property integer $id
 * @property integer $app_case_id
 * @property integer $report_type_id
 * @property integer $report_topic_id
 * @property string $incident_datetime
 * @property integer $recordable
 * @property integer $lost_time
 * @property integer $is_dart
 * @property integer $dart_time
 * @property integer $body_part_id
 * @property integer $injury_type_id
 *
 * @property AppCase $appCase
 * @property BodyPart $bodyPart
 * @property InjuryType $injuryType
 * @property ReportTopic $reportTopic
 * @property ReportType $reportType
 */
class AppCaseIncident extends \yii\db\ActiveRecord
{
	
	public $incident_date;
	public $incident_time;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_case_incident';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        return [
            [['lost_time'], 'default', 'value' => 0 ],
            [['dart_time'], 'default', 'value' => 0 ],
            [['app_case_id', 'report_type_id', 'report_topic_id', 'incident_datetime', 'recordable','is_property_damage','is_lost_time','is_dart'], 'required'], 
            [['app_case_id', 'report_type_id', 'report_topic_id', 'lost_time', 'injury_type_id', 'body_part_id','causation_factor','dart_time'], 'integer'], 
            [['incident_datetime', 'incident_date', 'incident_time'], 'safe'],
            ['causation_factor','required',
                'when' => function($model_detail){ 
                           return $model_detail->report_type_id == '3'; //show error message when report type is 'final'
               },
                'whenClient' => "function (attribute, value) { 
                  return $('#report_type_id').val() == '3'; 
              }"      
           ]    
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
            'report_type_id' => 'Report Type',
            'report_topic_id' => 'Report Topic',
            'incident_datetime' => 'Incident Datetime',
            'recordable' => 'Recordable Injury',
            'lost_time' => 'Lost Time Days',
            'is_property_damage' => 'Property Damage',
            'is_lost_time' => 'Lost Time Injury',
            'body_part_id' => 'Body Part',
            'injury_type_id' => 'Injury Type',
            'causation_factor' => 'Causation Factor',
            'is_dart' => 'Days Away, Restricted and Transfer (DART)',
            'dart_time' => 'DART Days'
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
    public function getBodyPart()
    {
        return $this->hasOne(BodyPart::className(), ['id' => 'body_part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInjuryType()
    {
        return $this->hasOne(InjuryType::className(), ['id' => 'injury_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportTopic()
    {
        return $this->hasOne(ReportTopic::className(), ['id' => 'report_topic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportType()
    {
        return $this->hasOne(ReportType::className(), ['id' => 'report_type_id']);
    }
    
     /**
     * @return caustion factor
     */
    public function getCausationFactor()
    {
        return $this->hasOne(CausationFactor::className(), ['id' => 'causation_factor']);
    }
}
