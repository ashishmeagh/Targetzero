<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;

	//get Jobsites
    $data_jobsite = ArrayHelper::map( app\models\Jobsite::find()->joinWith('userJobsites')->where([ "jobsite.is_active" => 1, "user_jobsite.user_id" => Yii::$app->session->get( "user.id" ) ])->orderBy('jobsite')->asArray()->all(), 'id', 'jobsite' );

    //get Buildings
	$data_building = ArrayHelper::map( app\models\Building::find()->where(['jobsite_id' => $data['jobsite_id']])->asArray()->all(), 'id', 'building');
	if( count($data_building) === 0 ){ $data_building = ['' => '-']; }
	//get Floors
	$data_floor = ArrayHelper::map( app\models\Floor::find()->where(['building_id' => $data['building_id']])->asArray()->all(), 'id', 'floor');
	if( count($data_floor) === 0 ){ $data_floor = ['' => '-']; }
	
?>
<div class="area-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <!-- Jobsite -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <label class="control-label" for="jobsite-id-select">Jobsite</label>
                <?= Html::dropDownList
                (
                    'jobsite_id',
                    $data['jobsite_id'],
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
                        buildingIdChange,
                        ['']
                    );
                    return false;
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
                    'building_id',
                    $data['building_id'],
                    $data_building,
                    [
                        'id' => 'building-id-select',
                        'class' => 'form-control',
                        'onchange' => 'buildingIdChange(this.value)',
                    ]
                )?>
            </div>
            <script>
                function buildingIdChange( $val )
                {
                    onChangeDropdown
                    (
                        '<?= Yii::$app->urlManager->createUrl('ajax/get-floor?id=') ?>'+$val,
                        '#floor-id-select',
                        'Floor',
                        'floor',
                        null,
                        ['']
                    );
                    return false;
                }
            </script>
        </div>
        <!-- Building -->
    </div>

    <div class="row">
        <!-- Floor -->
        <div class="col-sm-6">
            <div class="form-group fg-line">
                <label class="control-label" for="floor-id-select">Floor</label>
                <?= Html::dropDownList
                (
                    'Area[floor_id]',
                    $model->floor_id,
                    $data_floor,
                    [
                        'id' => 'floor-id-select',
                        'class' => 'form-control'
                    ]
                )?>
            </div>
        </div>
        <!-- Floor -->
        <div class="col-sm-6">
            <?= $form->field($model, 'area')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left m-t-10">
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="1" name="Area[is_active]" <?= ($model->is_active == 1) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Active
                </label>
                <label class="radio radio-inline m-r-20">
                    <input type="radio" value="0" name="Area[is_active]" <?= ($model->is_active == 0) ? 'checked' : '' ?> >
                    <i class="input-helper"></i>
                    Inactive
                </label>
            </div>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

