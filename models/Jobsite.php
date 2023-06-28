<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jobsite".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created
 * @property string $updated
 * @property string $jobsite
 * @property integer $timezone_id
 * @property integer $newsflash_allowed
 * @property integer $photo_allowed
 * @property string $city
 * @property string $state
 * @property integer $zip_code
 * @property string $exec_vp
 * @property string $sr_vp
 * @property string $wt_group
 * @property string $is_cmic
 *
 * @property AppCase[] $appCases
 * @property Building[] $buildings
 * @property UserJobsite[] $userJobsites
 * @property ContractorJobsite[] $contractorJobsites
 */
class Jobsite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jobsite';
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
            [['is_active', 'created', 'updated', 'jobsite', 'photo_allowed', 'newsflash_allowed', 'timezone_id'], 'required'],
            [['is_active', 'photo_allowed', 'zip_code','is_cmic','timezone_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['jobsite', 'job_number', 'address', 'city', 'state', 'exec_vp', 'sr_vp', 'wt_group'], 'string', 'max' => 255]
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
            'jobsite' => 'Jobsite',
            'timezone_id' => 'Timezone',
            'photo_allowed' => 'Photo Allowed',
            'newsflash_allowed' => 'Safety Alert Allowed',
            'job_number' => 'Job Number',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => 'ZipCode',
            'exec_vp' => 'Exec. VP',
            'sr_vp' => 'Sr. VP',
            'wt_group' => 'WT Group'

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppCases()
    {
        return $this->hasMany(AppCase::className(), ['jobsite_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildings()
    {
        return $this->hasMany(Building::className(), ['jobsite_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserJobsites()
    {
        return $this->hasMany(UserJobsite::className(), ['jobsite_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractorJobsites()
    {
        return $this->hasMany(ContractorJobsite::className(), ['jobsite_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimezone()
    {
        return $this->hasOne(Timezone::className(), ['id' => 'timezone_id']);
    }
    
    public function getJobsite($userId,$contractorId = null)
    {
     
       if($contractorId !=0)
       {
         $jobsiteByUser = Yii::$app->db->createCommand("select j.Id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id where uj.user_id = $userId")->queryAll();
         $assignedJobsites = Yii::$app->db->createCommand("select distinct j.Id,j.jobsite from contractor c left join [dbo].[contractor_jobsite] cj on c.id = cj.contractor_id left join jobsite j on cj.Jobsite_id = j.id where cj.contractor_id = $contractorId")->queryAll();
         $duplicateJobsite = Yii::$app->db->createCommand("select distinct j.Id,j.jobsite from contractor c left join [dbo].[contractor_jobsite] cj on c.id = cj.contractor_id
         left join jobsite j on cj.Jobsite_id = j.id
         left join user_jobsite uj on j.id = uj.jobsite_id where cj.contractor_id = $contractorId and 
         uj.User_id= $userId")->queryAll();
          $data_jobsite = array_merge($jobsiteByUser,$assignedJobsites);
          $listJobsite = array();
          foreach($data_jobsite as $a)
          {
              if(!in_array($a ,$listJobsite))
              {
                  $listJobsite[] = $a;
              }
          } 
       }
       else
       {
           $listJobsite = Yii::$app->db->createCommand("select j.Id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id where uj.user_id = $userId")->queryAll();
       }
    
         return $listJobsite;
    }
    
    public static function getDifferentJobsite($userId,$contractorId = null)
    {
         if($contractorId != 0)
       {
         $jobsiteByUser = Yii::$app->db->createCommand("select j.Id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id where uj.user_id = $userId")->queryAll();
         $assignedJobsites = Yii::$app->db->createCommand("select distinct j.Id,j.jobsite from contractor c left join [dbo].[contractor_jobsite] cj on c.id = cj.contractor_id left join jobsite j on cj.Jobsite_id = j.id where cj.contractor_id = $contractorId")->queryAll();

         $b1 =array();
            foreach($jobsiteByUser as $x)
             $b1[$x['Id']] = $x['jobsite'];
              
              $b2 =array();
              foreach($assignedJobsites as $x)
              $b2[$x['Id']] = $x['jobsite'];
                
              $c_intersect = array_intersect_key($b1,$b2);
             $c_1 = array_diff_key($b1,$b2);
              $c_2 = array_diff_key($b2,$b1);
             
              $intersect_array = array();
            foreach($c_intersect as $i=>$v)
              $intersect_array[] = array('Id'=>$i,'jobsite'=>$v);

           $only_a1 = array();
             foreach($c_1 as $i=>$v)
                $only_a1[] = array('Id'=>$i,'jobsite'=>$v);
              
                    $only_a2 = array();
              foreach($c_2 as $i=>$v)
                    $only_a2[] = array('Id'=>$i,'jobsite'=>$v);
           return $only_a2;
       }
    }

    public  function getLoggedInUserJobsites($userId,$contractorId = null)
    {
         if($contractorId != 0)
       {
     
         $jobsiteByUser = Yii::$app->db->createCommand("select j.Id,j.jobsite from jobsite j join [user_jobsite] uj on j.id = uj.Jobsite_id join [user] u on u.id = uj.user_id  where uj.user_id = $userId  and u.contractor_id = $contractorId")->queryAll();
      
           return $jobsiteByUser;
       }
        return false;
    }
    

}
