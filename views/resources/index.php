<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\searches\Resources */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resources';
$this->params[ 'breadcrumbs' ][ ] = $this->title;
?>

<!-- add resources -->
<!--<div class="modal fade in" id="add-materias-by-token" tabindex="-1" role="dialog" aria-hidden="true">-->
<!--    <div class="modal-dialog  fade in modal-sm">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h4 class="modal-title">Upload Resourse</h4>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <div class="row">-->
<!--                    <div class="col-md-12"><p>Ingrese el código de la materia</p>-->
<!--                        <input id="token" class="form-control"/></div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div id="modal-footer-buttons" class="modal-footer">-->
<!--                <button type="button" class="btn btn-link" data-dismiss="modal">Cancelar</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- add resources -->

<div class="resources-index">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode( $this->title ) ?>
            <?=
            Html::a( '<i class="md md-search"></i>', null, [
                'onclick' => '$(".colapsable").slideToggle(); return false;',
                'style' => '  cursor: pointer; display: inline-block; margin: 0 0 0 10px;',
            ] )
            ?>
        </h2>
        <ul class="actions">
            <li class="spinHover">
                <?= Html::a( '<i class="md md-add"></i>', [ 'create' ], [ 'class' => 'spinHover' ] ) ?>
            </li>
        </ul>
    </div>

    <?php Pjax::begin();; ?>
    <?php $form = ActiveForm::begin( [
        'method' => 'get',
    ] ); ?>

    <div class="row colapsable" style="display: none;">
        <div class="col-sm-12">
            <div class="col-sm-2">
                <?= $form->field( $searchModel, 'type_id' )->dropDownList( ArrayHelper::map( app\models\ResourcesType::find()->asArray()->all(), 'id', 'type' ), [ 'prompt' => '-Choose a type-' ] ) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field( $searchModel, 'title' )->textInput( [ 'placeholder' => 'Search title...' ] )->label( 'Title' ) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field( $searchModel, 'url' )->textInput( [ 'placeholder' => 'Search url...' ] )->label( 'URL' ) ?>
            </div>
            <?= Html::submitButton( '<i class="md md-search"></i>', [
                'class' => 'btn btn-primary waves-effect',
                'style' => 'margin-top: 25px;'
            ] ) ?>

        </div>
    </div>


    <?php ActiveForm::end(); ?>

    <?= ListView::widget( [
        'dataProvider' => $dataProvider,
        'itemOptions' => [ 'class' => 'item' ],
        'itemView' => 'view',
    ] ) ?>
    <?php Pjax::end(); ?>

</div>
