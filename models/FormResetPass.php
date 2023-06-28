<?php

namespace app\models;
use Yii;
use yii\base\Model;
use app\models\User;

class FormResetPass extends Model{

	public $email;

	public function rules(){
		return [
			[['email'], 'required'],
			['email', 'match', 'pattern' => "/^.{5,80}$/"],
			['email', 'email'],
			['email', 'email_existe'],
		];
	}

    /**
     * Comprueba que un email exista
     * @return \yii\db\ActiveQuery
     */
	public function email_existe($attribute, $params){
		//Buscar el email en la tabla
		$table = User::find()->where(['email' => $this->email])->one();

		//Si el email no existe mostrar el error
		if (!$table || $table == null){
			$this->addError($attribute, 'Email address unknown. Please enter a user existing one.');
		}
	}

}
