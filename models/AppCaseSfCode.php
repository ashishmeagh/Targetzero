<?php

    namespace app\models;

    use Yii;

    /**
     * This is the model class for table "app_case_sf_code".
     *
     * @property integer         $id
     * @property integer         $is_active
     * @property string          $created
     * @property string          $updated
     * @property string          $code
     * @property string          $description
     * @property integer         $parent_id
     *
     * @property AppCase[]       $appCases
     * @property AppCaseSfCode   $parent
     * @property AppCaseSfCode[] $appCaseSfCodes
     */
    class AppCaseSfCode extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'app_case_sf_code';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            date_default_timezone_set("America/Chicago");
            $this->updated = date( "Y-m-d H:i:s" );

            return [
                [
                    [
                        'created',
                        'updated'
                    ],
                    'default',
                    'value' => date( "Y-m-d H:i:s" )
                ],
                [
                    [
                        'is_active',
                        'created',
                        'updated',
                        'code',
                        'description'
                    ],
                    'required'
                ],
                [
                    [
                        'is_active',
                        'parent_id'
                    ],
                    'integer'
                ],
                [
                    [
                        'created',
                        'updated'
                    ],
                    'safe'
                ],
                [
                    [
                        'code'
                    ],
                    'string',
                    'max' => 255
                ]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'          => 'ID',
                'is_active'   => 'Active',
                'created'     => 'Created',
                'updated'     => 'Updated',
                'code'        => 'Code',
                'description' => 'Description',
                'parent_id'   => 'Parent ID',
            ];
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getAppCases()
        {
            return $this->hasMany( AppCase::className(), [ 'app_case_sf_code_id' => 'id' ] );
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getParent()
        {
            return $this->hasOne( AppCaseSfCode::className(), [ 'id' => 'parent_id' ] );
        }

        public function getParentCode( $sf_code_id )
        {
            //get building NAME
            $parent_code = AppCaseSfCode::find()->select( 'code' )->where( [ 'id' => $sf_code_id ] )->asArray()->all();

            return $parent_code[0]['code'];
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getAppCaseSfCodes()
        {
            return $this->hasMany( AppCaseSfCode::className(), [ 'parent_id' => 'id' ] );
        }
    }
