<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\widgets\ActiveForm;

    $this->title = 'Password recovery';
    AppAsset::register($this);
?>

<!-- Login -->
<div class="card">
    <div class="card-header">
        <?= Html::img( '@web/img/tz-logotype.svg', []) ?>
    </div>
    <div class="card-body p-25">
        <?php $form = ActiveForm::begin(["enableClientValidation"=>false]); ?>
        <div class="row">
            <div class="col-xs-12">
                <?= $form->field($model, 'email')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-6 col-xs-6">
				<?= Html::a(' Cancel', ['login/index']) ?>
                <?= Html::submitButton(' Send <i class="md md-arrow-forward"></i>', ['class' => 'btn btn-primary pull-right']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<!-- Login -->