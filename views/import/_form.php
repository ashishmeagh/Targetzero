<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Resources */
/* @var $form yii\widgets\ActiveForm */

$resources_type = ArrayHelper::map( app\models\ResourcesType::find()->asArray()->all(), 'id', 'type');

?>


<div class="resources-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-8">
            <?= $form->field( $model, 'title' )->textInput( [ 'maxlength' => 255 ] ) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field( $model, 'type_id' )->dropDownList( $resources_type, [
                'prompt'   => '-Choose a type-'
            ] ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field( $model, 'url' )->textarea() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field( $model, 'description' )->textarea( [ 'rows' => 6 ] ) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton( $model->isNewRecord ? 'Create' : 'Update', [ 'class' => 'btn btn-primary pull-right' ] ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
