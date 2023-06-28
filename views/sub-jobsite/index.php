<?php

use yii\helpers\Html;
use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\models\searches\SubJobsite */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sub jobsites';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-jobsite-index">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <ul class="actions">
            <?php  if (Yii::$app->session->get( "user.role_id" ) == ROLE_SYSTEM_ADMIN || Yii::$app->session->get( "user.role_id" ) == ROLE_ADMIN): ?>
                <li>
                    <?= Html::a('<i class="md md-add"></i>', ['create'], ['class' => '','style' => 'display:none']) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="card">
        <div class="card-body p-t-15">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table table-hover'],
                'summary' => false,
                'columns' => [
                    [
                        'attribute' => 'is_active',
                        'format' => 'html',
                        'value' => function($data)
                        {
                            if($data->is_active == true)
                            {
                                return Html::tag('i', '', ['class' => 'md md-check is-active']);
                            }
                            else
                            {
                                return Html::tag('i', '', ['class' => 'md md-close is-active']);
                            }
                        },
                        'contentOptions'=>['class'=>'active-column'],
                        'filter' => array('1' => 'Active', '0' => 'Inactive'),
                    ],
                    [
                        'attribute' => 'subjobsite',
                        'format' => 'raw',
                        'value' => function($data)
                        {
                            if (Yii::$app->session->get( "user.role_id" ) == ROLE_SYSTEM_ADMIN || Yii::$app->session->get( "user.role_id" ) == ROLE_ADMIN)
                            {
                                return Html::a( $data->subjobsite, ['/sub-jobsite/update?id='.$data->id],['data-pjax' => 0,'target' => '_blank']);
                            }else{
                                return "<p class='m-0'>".$data->subjobsite."</p>";
                            }
                        },
                    ],
                    [
                        'attribute' => 'jobsite_id',
                        'value' => 'jobsite.jobsite',
                        'label' => 'Jobsite',
                    ],
                    [
                        'attribute' => 'created',
                        'format' => ['date', 'php:M d, Y'],
                        'contentOptions'=>['class' => 'date-column', 'style' => 'width: 125px;'],
                    ],
                    [
                        'attribute' => 'updated',
                        'format' => ['date', 'php:M d, Y'],
                        'contentOptions'=>['class' => 'date-column', 'style' => 'width: 125px;'],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
