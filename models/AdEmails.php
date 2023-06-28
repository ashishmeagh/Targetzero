<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content".
 *
 * @property integer $id
 * @property string $email
 * @property string $created_by
 * @property string $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string $sourcegroup
 *
 */
class AdEmails extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ad_emails';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $this->created_at = date("Y-m-d H:i:s");
        return [
            [['created_at', 'updated_at'], 'default', 'value' => date("Y-m-d H:i:s")],
            [['status'], 'default', 'value' => 0],            
            [['email','username','firstname','lastname', 'sourcegroup'], 'trim'],
            [['email','username'], 'unique']
        ];
    }

}
