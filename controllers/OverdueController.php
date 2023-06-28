<?php

namespace app\controllers;

use Yii;

class OverdueController extends \yii\web\Controller
{

    public $enableCsrfValidation = FALSE;

    public function actionIndex()
    {
        $date = date('Y/m/d H:i:s');
        $overdue_state = APP_CASE_STATUS_OVERDUE;
        $open_state = APP_CASE_STATUS_OPEN;
        Yii::$app->db->createCommand("update app_case_recognition acr join app_case ac on acr.app_case_id = ac.id set ac.updated = '$date', ac.app_case_status_id = '$overdue_state' where acr.correction_date <= '$date' and ac.app_case_status_id = '$open_state'")->execute();
        echo "Cron realizado con exito";
//        return $this->redirect( array( 'app-case/index' ) );
    }

}
