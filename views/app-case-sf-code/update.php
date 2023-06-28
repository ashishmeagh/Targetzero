<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Update Safety Code / ' . ' ' . $model->code;
    $this->params['breadcrumbs'][] = ['label' => 'Safety Codes', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Update: '.$model->code;
?>
<div class="app-case-sf-code-update">

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
            <?= $this->render('_form', [ 'model' => $model, 'safetyCodeParentName' => $safetyCodeParentName, ]) ?>
        </div>
    </div>
</div>
