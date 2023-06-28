<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_case".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property integer $creator_id
 * @property string $updated
 * @property integer $jobsite_id
 * @property integer $sub_jobsite_id
 * @property integer $building_id
 * @property integer $floor_id
 * @property integer $area_id
 * @property integer $contractor_id
 * @property integer $affected_user_id
 * @property integer $app_case_type_id
 * @property integer $app_case_status_id
 * @property integer $app_case_sf_code_id
 * @property integer $app_case_priority_id
 * @property integer $trade_id
 * @property string $additional_information
 * @property integer $platform_id
 *
 * @property User $affectedUser
 * @property AppCasePriority $appCasePriority
 * @property AppCaseSfCode $appCaseSfCode
 * @property AppCaseStatus $appCaseStatus
 * @property AppCaseType $appCaseType
 * @property Area $area
 * @property Building $building
 * @property Contractor $contractor
 * @property User $creator
 * @property Floor $floor
 * @property Jobsite $jobsite
 * @property Trade $trade
 * @property AppCaseHistory[] $appCaseHistories
 * @property AppCaseIncident[] $appCaseIncidents
 * @property AppCaseObservation[] $appCaseObservations
 * @property AppCaseRecognition[] $appCaseRecognitions
 * @property AppCaseViolation[] $appCaseViolations
 * @property Comment[] $comments
 * @property Content[] $contents
 * @property Follower[] $followers
 * @property Notification[] $notifications
 */
class AppCase extends \yii\db\ActiveRecord
{
    var $newsflash_allowed;
    var $photo_allowed;
   var $attachments;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_case';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // date_default_timezone_set("America/Chicago");
        // $this->updated = date("Y-m-d H:i:s");
        return [
            /*[['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],*/
            [['affected_user_id', 'is_active', 'created', 'creator_id', 'updated', 'jobsite_id','building_id', 'floor_id', 'contractor_id', 'app_case_type_id', 'app_case_status_id', 'app_case_sf_code_id', 'app_case_priority_id', 'trade_id', 'additional_information'], 'required'],
            [['is_active', 'creator_id', 'jobsite_id', 'area_id', 'sub_jobsite_id',  'contractor_id', 'affected_user_id', 'app_case_type_id', 'app_case_status_id', 'app_case_sf_code_id', 'app_case_priority_id', 'trade_id','is_attachment','platform_id'], 'integer'],
            [['created', 'updated', 'updated_gmt'], 'safe'],
            [['additional_information'], 'string']
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
            'creator_id' => 'Owner',
            'updated' => 'Updated',
            'jobsite_id' => 'Jobsite',
            'sub_jobsite_id' => 'Sub Jobsite',
            'building_id' => 'Building',
            'floor_id' => 'Floor',
            'area_id' => 'Area',
            'contractor_id' => 'Contractor',
            'affected_user_id' => 'Affected Employee',
            'app_case_type_id' => 'Type',
            'app_case_status_id' => 'Status',
            'app_case_sf_code_id' => 'Safety Code',
            'app_case_priority_id' => 'Priority',
            'trade_id' => 'Trade',
            'is_attachment' => 'Attachment',
            'additional_information' => ($this->app_case_type_id == 3)?'Additional Information':'Description'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffectedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'affected_user_id'])->from(['affecteduser' => User::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCasePriority()
    {
        return $this->hasOne(AppCasePriority::className(), ['id' => 'app_case_priority_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseSfCode()
    {
        return $this->hasOne(AppCaseSfCode::className(), ['id' => 'app_case_sf_code_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseStatus()
    {
        return $this->hasOne(AppCaseStatus::className(), ['id' => 'app_case_status_id']);
    }

	public function getStatusName()
	{
		$model = $this->appCaseStatus;
        return $model ? $model->status : '';

	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseType()
    {
        return $this->hasOne(AppCaseType::className(), ['id' => 'app_case_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Contractor::className(), ['id' => 'contractor_id']);
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
    public function getFloor()
    {
        return $this->hasOne(Floor::className(), ['id' => 'floor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobsite()
    {
        return $this->hasOne(Jobsite::className(), ['id' => 'jobsite_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubJobsite()
    {
        return $this->hasOne(SubJobsite::className(), ['id' => 'sub_jobsite_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrade()
    {
        return $this->hasOne(Trade::className(), ['id' => 'trade_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseHistories()
    {
        return $this->hasMany(AppCaseHistory::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseIncidents()
    {
        return $this->hasMany(AppCaseIncident::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseObservations()
    {
        return $this->hasMany(AppCaseObservation::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseRecognitions()
    {
        return $this->hasMany(AppCaseRecognition::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseViolations()
    {
        return $this->hasMany(AppCaseViolation::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContents()
    {
        return $this->hasMany(Content::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowers()
    {
        return $this->hasMany(Follower::className(), ['app_case_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['app_case_id' => 'id']);
    }
    
    
    public function getPlatform($platform_id)
	{
		$platform = Platform::find()->select('platform')->where(['id' => $platform_id])->one();
		return ($platform['platform']?? '');
	}

    
}
