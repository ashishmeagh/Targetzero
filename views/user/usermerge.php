<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Merge User / ' . ' ' . $model->first_name . ' ' . $model->last_name;
    $this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Merge: '.$model->first_name . ' ' . $model->last_name;
?>
<div class="user-update">

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

         <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
           <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('error') ?></strong>
        </div>
    <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body card-padding">
            <?= $this->render('_usermergeform', [ 'model' => $model, ]) ?>
        </div>
    </div>
</div>