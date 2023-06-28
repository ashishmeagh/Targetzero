<?php
namespace app\helpers;


use Yii;
use yii\db\Query;
use bryglen\sendgrid;
use yii\helpers\Url;

class emailValidation {

	public static  function SendEmailValidation($email, $id) {

	    //Sent Email Veriffication
       $email_ar = explode(', ', $email);
      // $url = Url::base(true)."/custom-user/email-verify-check?data=". base64_encode($id);  
       
       $confirmLink = "https://targetzerowt.com/custom-user/email-verify?data=". base64_encode($id);
       $message = "Email Verification"; 
	    Yii::$app->mailer->compose('email-verifycheck', ['logo_wt' => '../mail/images/logo.png', 'confirmLink' => $confirmLink])
                    ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                    ->setTo($email_ar)
                    ->setSubject("$message")
                    ->send();

	}

}