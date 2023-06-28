<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\searches\AppCase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-case-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php // echo $form->field($model, 'id') ?>
    
    <?php echo $form->field($model, 'is_active') ?>
    
    <?php // echo $form->field($model, 'created') ?>
    
    <?php // echo $form->field($model, 'creator_id') ?>
	
    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'jobsite_id') ?>

    <?php // echo $form->field($model, 'building_id') ?>

    <?php // echo $form->field($model, 'floor_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'contractor_id') ?>

    <?php // echo $form->field($model, 'affected_user_id') ?>

    <?php // echo $form->field($model, 'app_case_type_id') ?>

    <?php // echo $form->field($model, 'app_case_status_id') ?>

    <?php // echo $form->field($model, 'app_case_sf_code_id') ?>

    <?php // echo $form->field($model, 'app_case_priority_id') ?>

    <?php // echo $form->field($model, 'trade_id') ?>

    <?php // echo $form->field($model, 'additional_information') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
