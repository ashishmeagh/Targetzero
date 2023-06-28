<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;

    $this->title = 'Trades';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="trade-index">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <ul class="actions">
            <li>
                <?= Html::a('<i class="md md-add"></i>', ['create'], ['class' => '']) ?>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body p-t-15">
			<?php
			if( Yii::$app->session->get('user.role_id') != ROLE_ADMIN ){
				$visible_colum = false;
			}else{
				$visible_colum = true;
			}
			?>
			<?php \yii\widgets\Pjax::begin(); ?>
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
                        'attribute' => 'trade',
                        'format' => 'html',
                        'value' => function($data)
                        {
                            return Html::a( $data->trade, ['/trade/update?id='.$data->id]);
                        },
                        'label' => 'Trade',
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
                        'label' => 'Delete',
						'value' => function($data){
							return Html::a('<i class="md md-delete view-case"></i>', null, [
								'class' => 'delete-item',
								'style' => 'cursor: pointer;',
								'onclick' => 'deleteItem('.$data->id.');',
							]);
						},
						'visible' => $visible_colum,
						'contentOptions'=>['style' => 'width: 50px; text-align: center;'],
                    ],
                ],
            ]); ?>
			<?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>
</div>

<script>

	var deleteItem = function(item_id){
		
		swal({ 
			title: "Are you sure?",   
			text: "You will not be able to recover this item!",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: false 
		}, function(isConfirm){
			if(isConfirm){
                executeAjax("<?= Yii::$app->urlManager->createUrl('trade/delete?id=') ?>"+item_id).done(function(r){
					if(r){
						swal("Deleted!", "The item has been deleted.", "success");
						$.pjax.reload({container:'#w0'});
					}else{
						swal("Error!", "This item is in use and cannot be deleted.", "error");
					}
				}).fail(function(x){
					swal("Error!", "This item is in use and cannot be deleted.", "error");
				})
			}
			
		});
		return false;
		
	}

</script>