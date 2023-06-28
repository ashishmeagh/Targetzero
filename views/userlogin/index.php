<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\widgets\ActiveForm;
    
    $this->title = 'Target Zero';
    AppAsset::register($this);
?>

<!-- Login -->
<div class="card">
    <div class="card-header">
        <?= Html::img( '@web/img/tz-logotype.svg', []) ?>
    </div>
    <div class="card-body p-25">
        <?php $form = ActiveForm::begin(["enableClientValidation"=>false]); ?>
        <input style="display:none">
        <input type="password" style="display:none">
        <div class="row">
            <div class="col-xs-12">
                <?= $form->field($model, 'user_name')->textInput(['maxlength' => 20]) ?>
                <?= $form->field($model, 'password')->passwordInput(['autocomplete' =>'off']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <div class="checkbox m-t-10 m-b-0">
                    <label>
                        <input name="remember" type="checkbox" value="">
                        <i class="input-helper"></i>
                        Keep me logged in
                    </label>
                </div>
            </div>
            <div class="col-xs-6">
                <?= Html::submitButton(' Log in <i class="md md-arrow-forward"></i>', ['class' => 'btn btn-primary pull-right']) ?>
                <?= Html::a(' Forgot your password?', ['userlogin/resetpassword']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<!-- Login -->
