<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $role_id
 * @property string $user_name
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $division
 * @property string $employee_number
 * @property string $password
 * @property integer $contractor_id
 * @property integer $sop
 * @property string $emergency_contact_name
 * @property integer $emergency_contact
 * @property string $digital_signature
 * @property integer $IsAduser
 *
 * @property AppCase[] $appCases
 * @property AppCaseHistory[] $appCaseHistories
 * @property AppCaseObservation[] $appCaseObservations
 * @property AppCaseRecognition[] $appCaseRecognitions
 * @property AppCaseViolation[] $appCaseViolations
 * @property Comment[] $comments
 * @property Device[] $devices
 * @property Follower[] $followers
 * @property Notification[] $notifications
 * @property Session[] $sessions
 * @property Contractor $contractor
 * @property Role $role
 * @property UserJobsite[] $userJobsites
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $changePassword;

    public static function tableName()
    {
        return 'dbo.user';
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
            [['is_active', 'created', 'updated', 'role_id', 'first_name', 'last_name', 'employee_number', 'contractor_id'], 'required'],
            [['is_active', 'role_id', 'contractor_id', 'default_jobsite', 'sop','IsAduser'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_name'], 'string', 'max' => 50],
            [['first_name', 'last_name', 'division'], 'string', 'max' => 70],
            [[ 'phone'],  'match', 'pattern' => '/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/', 'message' => 'Please enter valid Phone Number.'],
            [['emergency_contact'],  'match', 'pattern' => '/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/', 'message' => 'Please enter valid Emergency Contact Number.','when' => function($model) {return $this->emergency_contact != 0 && $model->role_id == 19;}],
            [['email', 'password', 'employee_number'], 'string', 'max' => 255],
            [['emergency_contact_name', 'digital_signature'], 'string'],
            [['email'], 'email'],
            [['user_name', 'email'], 'trim'],
            [['user_name'], 'unique'],            
            ['user_name', 'required', 'when' => function($model) {                    
                        return in_array($model->role_id,  array(1,2,3,4,5,6,18));
                },'whenClient' => "function (attribute, value) {
            return $('#user-role_id').val() == 1||$('#user-role_id').val() == 2 ||$('#user-role_id').val() == 3||$('#user-role_id').val() == 4||$('#user-role_id').val() == 5||$('#user-role_id').val() == 6||$('#user-role_id').val() == 18;
        }"]
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
            'role_id' => 'Role',
            'user_name' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'division' => 'Division',
            'employee_number' => 'Employee number',
            'password' => 'Password',
            'contractor_id' => 'Contractor',
            'default_jobsite' => 'Default jobsite',
            'sop' => 'SOP',
            'emergency_contact' => 'Emergency Contact Number',
            'emergency_contact_name' => 'Emergency Contact Name',
            'digital_signature' => 'Digital Signature',
            'fullName' => Yii::t('app', 'Name'),
            'IsAduser' => 'IsAduser'
        ];
    }

	/**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        /* Valida el password */
        if( md5($password) == $this->password){
			return $password === $password;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseHistories()
    {
        return $this->hasMany(AppCaseHistory::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseObservations()
    {
        return $this->hasMany(AppCaseObservation::className(), ['foreman_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseRecognitions()
    {
        return $this->hasMany(AppCaseRecognition::className(), ['foreman_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCaseViolations()
    {
        return $this->hasMany(AppCaseViolation::className(), ['foreman_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowers()
    {
        return $this->hasMany(Follower::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::className(), ['user_id' => 'id']);
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
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserJobsites()
    {
        return $this->hasMany(UserJobsite::className(), ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChangesTrackers()
    {
        return $this->hasMany(ChangesTracker::className(), ['user_id' => 'id']);
    }
        /**
         * @return \yii\db\ActiveQuery
         */
        public function getLoginTrackers()
    {
        return $this->hasMany(LoginTracker::className(), ['user_id' => 'id']);
    }
    
    public static function getUserStatus()
    {
       return array(['id' => '1','name' => 'Active'],['id' => '0','name' => 'InActive']);
    }
  
    public function getStatusName()
	{
		$model = $this->is_active;
                if($this->is_active == 1)
                {
                    return 'Active';
                }
                else
                {
                     return 'InActive';
                }
	}

    public function  getUsersByJobisites($userId)
    {
        $sqlQuery = (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ? "SELECT u.id, u.email, u.first_name, u.last_name  FROM [user] u where u.is_active = 1 and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name" : "SELECT u.id, u.email, u.first_name, u.last_name FROM [user] u left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id left join [dbo].[user_jobsite] J on UJ.jobsite_id = J.jobsite_id where j.user_id = ".$userId." and u.is_active = 1 and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name";

       $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
   foreach ($usersList as $key => $value) { 
          $userListdataArray[$value['id']] = $value['first_name'].' '. $value['last_name'];
      }

      return $userListdataArray;
    }

         public  function getAffectedUsersByJobisites($userId,$contractorId = null)
    {
         $sqlQuery = (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ? "SELECT u.id, u.email, u.first_name, u.last_name FROM [user] u left join [dbo].[app_case] ac on ac.affected_user_id = u.id where u.is_active = 1 and u.role_id in (1,2,3,4,5,6,16)  group by u.id,u.email, u.first_name, u.last_name" : "SELECT u.id, u.email, u.first_name, u.last_name FROM [user] u  left join [dbo].[app_case] ac on ac.affected_user_id = u.id left join [dbo].[user_jobsite] UJ on UJ.user_id = u.id left join [dbo].[user_jobsite] J on UJ.jobsite_id = J.jobsite_id where j.user_id = ".$userId." and u.is_active = 1 and u.role_id in (1,2,3,4,5,6,16) group by u.id,u.email, u.first_name, u.last_name";

       $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
        $userListdataArray = array();
   foreach ($usersList as $key => $value) { 
          $userListdataArray[$value['id']] = $value['first_name'].' '. $value['last_name'];
      }

      return $userListdataArray;
    }

       public  function getData_creator()
    {
         $sqlQuery = "select Top 1000 u.id, u.email, u.first_name, u.last_name, u.employee_number  from [dbo].[user] u where u.is_active = 1 order by u.employee_number";

       $usersList = Yii::$app->db->createCommand("$sqlQuery")->queryAll();
       
      return $usersList;
    }
  }
