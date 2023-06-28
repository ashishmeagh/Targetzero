<?php

namespace app\models;

use Yii;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CausationFactor extends \yii\db\ActiveRecord
{
   /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'causation_factor';
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
            [['is_active', 'created', 'updated', 'type'], 'required'],
            [['is_active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['causation_factor'], 'string', 'max' => 255]
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
            'causation_factor' => 'Causation Factor',
        ];
    }

}
