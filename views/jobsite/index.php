<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;
    use yii\widgets\ActiveForm;

    $this->title = 'Jobsites';
    $this->params['breadcrumbs'][] = $this->title;
    $actionsTitle = "Edit";
?>
<div class="jobsite-index">

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
            <?php $form = ActiveForm::begin([
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-sm-3 col-sm-offset-8 text-right">
                    <?= $form->field($searchModel, 'jobsite')->textInput(['placeholder' => 'Find jobsite ...'])->label(false) ?>
                </div>
                <div class="col-sm-1">
                    <?= Html::submitButton('<i class="md md-search"></i>', ['class' => 'btn btn-primary waves-effect', 'style' => 'margin-top: 4px;']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
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
                        'contentOptions'=>['class' => 'active-column'],
                        'filter' => array('1' => 'Active', '0' => 'Inactive'),
                    ],
                    [
                        'attribute' => 'jobsite',
                        'format' => 'raw',
                        'value' => function($data)
                        {
                            if (Yii::$app->session->get( "user.role_id" ) == ROLE_SYSTEM_ADMIN || Yii::$app->session->get( "user.role_id" ) == ROLE_ADMIN)
                            {
                                return Html::a( $data->jobsite, ['/jobsite/view?id='.$data->id],['data-pjax' => 0,'target' => '_blank']);
                            }else{

                                return "<p class='m-0'>".$data->jobsite."</p>";
                            }
                        },
                        'label' => 'Jobsite',
                    ],
                    [
                        'attribute' => 'timezone_id',
                        'format' => 'html',
                        'value' => function($data)
                        {
                            if(is_null($data->timezone_id))
                            {
                                return "N/A";
                            }
                            else
                            {
                                return $data->timezone->timezone;
                            }
                        },
                        'label' => 'Timezone',
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
                    [
                        'attribute' => 'actions',
                        'format' => 'raw',
                        'label' => $actionsTitle,
                        'contentOptions' => [ 'class' => 'table-action-button' ],
                        'value' => function($data){
                          return Html::a( Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] ), [ '/jobsite/update?id='.$data->id ]);
                        }
                    ]
                ],
            ]); ?>
        </div>
    </div>
</div>
