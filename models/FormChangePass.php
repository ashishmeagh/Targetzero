<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class FormChangePass extends Model{
	
	public $old_password;
	public $password;
	public $password_repeat;
	
	public $_user = false;

	public function rules(){
		return [
			[['old_password', 'password', 'password_repeat'], 'required'],
			['password', 'match', 'pattern' => "/^.{5,16}$/"],
			['password_repeat', 'compare', 'compareAttribute' => 'password'],
			['old_password', 'validatePassword'],
		];
	}
	
	/**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne( Yii::$app->session->get('user.id') );
            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, 'Incorrect Current Password');
            }
        }
    }

}