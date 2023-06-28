<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Create Safety Code';
    $this->params['breadcrumbs'][] = ['label' => 'Safety Codes', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Create';
?>
<div class="app-case-sf-code-create">

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

