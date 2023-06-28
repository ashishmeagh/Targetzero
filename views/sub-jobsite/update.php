<?php

    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;


    /* @var $this yii\web\View */
/* @var $model app\models\SubJobsite */

$this->title = 'Update Sub jobsite: ' . ' ' . $model->subjobsite;
$this->params['breadcrumbs'][] = ['label' => 'Sub jobsites', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update: '.$model->subjobsite;
?>
<div class="sub-jobsite-update">
    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <ul class="actions">
            <li>
                <?= Html::a('<i class="md md-close"></i>', ['index']) ?>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body card-padding">
            <?= $this->render('_form', [ 'model' => $model, ]) ?>
        </div>
    </div>
</div>
