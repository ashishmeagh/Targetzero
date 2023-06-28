<?php
    /**
     * Created by IntelliJ IDEA.
     * User: imilano
     * Date: 02/05/2015
     * Time: 04:22 PM
     */
    namespace app\components;

    use Yii;
    use app\components\issueData;


    class jobsiteData
    {
        static function photoAllowed($app_case_id){
            $app_case = Yii::$app->db->createCommand("SELECT jobsite_id FROM app_case WHERE app_case.id = '$app_case_id'")->queryOne();
            $jobsite_id = $app_case["jobsite_id"];
            $jobsite = Yii::$app->db->createCommand("SELECT photo_allowed FROM jobsite WHERE jobsite.id = '$jobsite_id'")->queryOne();
            return $jobsite["photo_allowed"];
        }
    }