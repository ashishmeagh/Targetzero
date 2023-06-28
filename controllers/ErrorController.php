<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class ErrorController extends Controller
{

	// Properties
    public $layout = 'error-page';

	public function actionIndex()
    {
    	return $this->render('index');
    }

    public function actionAccessDenied()
    {
    	
    	return $this->render('accessdenied');
    }


}