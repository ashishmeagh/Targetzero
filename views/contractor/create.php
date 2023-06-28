<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Create Contractor';
    $this->params['breadcrumbs'][] = ['label' => 'Contractor', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Create';
?>
<div class="contractor-create">

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