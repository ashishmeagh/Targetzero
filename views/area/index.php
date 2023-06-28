<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Areas';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-index">

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
                    <?= Html::a('<i class="md md-add"></i>', ['create'], ['class' => '']) ?>
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
                        'attribute' => 'area',
                        'format' => 'raw',
                        'value' => function($data)
                        {
                            if (Yii::$app->session->get( "user.role_id" ) == ROLE_SYSTEM_ADMIN || Yii::$app->session->get( "user.role_id" ) == ROLE_ADMIN)
                            {
                                return Html::a( $data->area, ['/area/update?id='.$data->id],['data-pjax' => 0,'target' => '_blank']);
                            }else{

                                return "<p class='m-0'>".$data->area."</p>";
                            }
                        },
                        'label' => 'Area',
                    ],
                    [
                        'attribute' => 'floor_id',
                        'value' => 'floor.floor',
                        'label' => 'Floor',
                    ],
					[
                        'attribute' => 'Building',
                        'label' => 'Building',
						'format' => 'html',
                        'value' => function($data){
							return $data->getBuilding($data->floor_id);
						},
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
