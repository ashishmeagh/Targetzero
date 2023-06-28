<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "NewUserSelfRegistration".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property integer $jobsite_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $emergency_contact
 * @property string $phone
 * @property string $emergency_contact_name
 * @property integer $contractor_id
 * @property string $employee_number
 * @property integer $agreed
 * @property string $digital_signature
 * @property string $username
 *
 */
class NewUserSelfRegistration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'dbo.new_user_self_registration';
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
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['is_active'], 'default', 'value' => 1 ],
            [['created', 'jobsite_id', 'first_name', 'last_name', 'emergency_contact_name', 'emergency_contact', 'contractor_id', 'employee_number', 'agreed', 'digital_signature'], 'required'],
            [['created', 'updated'], 'safe'],
            [['jobsite_id', 'emergency_contact', 'contractor_id', 'agreed', 'is_active'], 'integer'],
            [['first_name','last_name','employee_number','digital_signature', 'email', 'username'], 'string'],
            ['email', 'required', 'when' => function($model) {                    
                return in_array($model->role_id,  array(1,2,3,4,5,6,7,8,10,11,12,13,15,16,18,19));
        }]
           ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'role_id' => 'Role',
            'is_active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
            'jobsite_id' => 'jobsite_id',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'emergency_contact' => 'emergency_contact',
            'contractor_id' => 'contractor_id',
            'agreed' => 'agreed',
            'employee_number' => 'Employee number',
            'digital_signature' => 'DigitalSignature',
            'email' => 'Email',
            'emergency_contact_name' => 'Emergency Contact Name'
        ];
    }

   
  }
