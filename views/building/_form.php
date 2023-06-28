<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
	
	//get Jobsites
    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->joinWith('userJobsites')->where([ "jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get( "user.id" ) ])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );

?>
<div class="building-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'jobsite_id')->dropDownList( $data_jobsite, ['prompt' => '-Choose a Jobsite-'] ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'building')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'location')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Building[is_active]" <?= ($model->is_active == 1) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Building[is_active]" <?= ($model->is_active == 0) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

