<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
	
	//get Jobsites
    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->joinWith('userJobsites')->where([ "jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get( "user.id" ) ])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );

    //get Buildings
	$data_building = ArrayHelper::map( app\models\Building::find()->where(["jobsite_id"=>$data["jobsite_id"]])->asArray()->all(), 'id', 'building');
    if( count($data_building) === 0 ){ $data_building = ['' => '-']; }
	
?>
<div class="floor-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <!-- Jobsite -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <label class="control-label" for="jobsite-id-select">Jobsite</label>
                <?= Html::dropDownList
                (
                    'jobsite_id',
                    $data["jobsite_id"],
                    $data_jobsite,
                    [
                        'prompt' => '-Choose a Jobsite-',
                        'id' => 'jobsite-id-select',
                        'class' => 'form-control',
                        'onchange' => 'jobsiteIdChange(this.value)',
                    ]
                )?>
            </div>
            <script>
                function jobsiteIdChange( $val )
                {
                    onChangeDropdown
                    (
                        '<?= Yii::$app->urlManager->createUrl('ajax/get-building?id=') ?>'+$val,
                        '#building-id-select',
                        'Building',
                        'building',
                        null,
                        ['']
                    );
                }
            </script>
        </div>
        <!-- Jobsite -->

        <!-- Building -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <label class="control-label" for="building-id-select">Building</label>
                <?= Html::dropDownList
                (
                    'Floor[building_id]',
                    $model->building_id,
                    $data_building,
                    [
                        'id' => 'building-id-select',
                        'class' => 'form-control'
                    ]
                )?>
            </div>
        </div>
        <!-- Building -->

    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'floor')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Floor[is_active]" <?= ($model->is_active == 1) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Floor[is_active]" <?= ($model->is_active == 0) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

