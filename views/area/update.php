<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Update Area / ' . ' ' . $model->area;
    $this->params['breadcrumbs'][] = ['label' => 'Areas', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Update: '.$model->area;
?>
<div class="area-update">

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
            <?= $this->render('_form', [ 'model' => $model, 'data' => $data ]) ?>
        </div>
    </div>
</div>
