<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job_errors".
 *

 * @property string $user_name
 * @property string $message
 
 */
class JobErrors extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_errors';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        return [
            [['created', 'updated'], 'default', 'value' => date("Y-m-d H:i:s") ],
            [['user_name', 'message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_name' => 'UserName',
            'message' => 'Message',
        ];
    }

    
}
