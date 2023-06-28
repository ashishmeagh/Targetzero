<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;

class DashboardController extends AllController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
