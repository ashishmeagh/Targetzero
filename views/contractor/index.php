<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;	
    use yii\widgets\ActiveForm;
    use yii\bootstrap\Modal;

    $this->title = 'Contractor';
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="contractor-index">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
        <?php if(Yii::$app->session->hasFlash('jobsite')): ?>
            <div class="alert alert-success alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
           <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('jobsite') ?></strong>
        </div>
        <?php endif; ?> 
        <ul class="actions">
            <li>
               
            </li>
        </ul>
    </div>
    <?php
        Modal::begin([
            'header' => '<h4 style="color:white;">Add Jobsites</h4>',
            'id' => 'modal',
            'closeButton' => [
                'id'=>'close-button',
                'class'=>'close',
                'data-dismiss' =>'modal',
            ],
            //keeps from closing modal with esc key or by clicking out of the modal.
            // user must click cancel or X to close
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => TRUE]
        ]);
        echo "<div id='modalContent'></div>";
        Modal::end();
        ?>
    <div class="card">
        <div class="card-body p-t-15">

            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => array('')
            ]); ?>

            <div class="row">
                <div class="col-sm-3 col-sm-offset-8 text-right">
                    <?= $form->field($searchModel, 'contractor')->textInput(['placeholder' => 'Find contractor ...'])->label(false) ?>
                </div>
                <div class="col-sm-1">
                    <?= Html::submitButton('<i class="md md-search"></i>', ['class' => 'btn btn-primary waves-effect', 'style' => 'margin-top: 4px;']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table table-hover', 'id' => 'contractor' ],
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
                        'attribute' => 'contractor',
                        'format' => 'raw',
                        'contentOptions' => [ 'class' => 'truncate-container' ],
                        'value' => function($data)
                        {
                            return Html::a( $data->contractor, ['/contractor/view?id='.$data->id], ['class' => 'truncate','data-pjax' => 0,'target' => '_blank'] );
                        },
                        'label' => 'Contractor',
                    ],
                    [
                        'attribute' => 'vendor_number',
                        'format' => 'html',
                        'contentOptions' => [ 'class' => 'truncate-container' ],
                        'value' => 'vendor_number',
                        'label' => 'Vendor number',
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
                        'label' => 'Add Jobsite',
                        'contentOptions' => [ 'class' => 'table-action-button' ],
                        'value' => function($data){   
                        if($data->cmic_updated != null){                        
                            if( Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL){
                                return Html::a( Html::tag( 'i', '', [ 'class' => 'md-add-circle view-case showModalButton','value'=>'/contractor/add-jobsite?id='.$data->id, 'style'=>'margin-left:20px;' ] ));
                                //return Html::a( Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] ), [ '/contractor/add-jobsite?id='.$data->id ],['data-pjax' => 0,'target' => '_blank']);
                            }
                            if($data->creator_id == Yii::$app->session->get('user.id')){
                                return Html::a( Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] ), [ '/contractor/update?id='.$data->id ],['data-pjax' => 0,'target' => '_blank']);
                            }else{
                                return Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] );
                            }
                        }else{
                                return Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] );
                            }
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<script language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript">
    //get the click of modal button to create / update item
    //we get the button by class not by ID because you can only have one id on a page and you can
    //have multiple classes therefore you can have multiple open modal buttons on a page all with or without
    //the same link.
//we use on so the dom element can be called again if they are nested, otherwise when we load the content once it kills the dom element and wont let you load anther modal on click without a page refresh
      $(document).on('click', '.showModalButton', function(){
        // window.onbeforeunload = () => {
        //     return "";
        //     };
        //check if the modal is open. if it's open just reload content not whole modal
        //also this allows you to nest buttons inside of modals to reload the content it is in
        //the if else are intentionally separated instead of put into a function to get the 
        //button since it is using a class not an #id so there are many of them and we need
        //to ensure we get the right button and content.
        
        if ($('#modal').data('bs.modal').isShown) {
            $('#modal').find('#modalContent')
                    .load($(this).attr('value'));
            //dynamiclly set the header for the modal via title tag
            //document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('title') + '</h4>';
        } else {
            //if modal isn't open; open it and load content
            $('#modal').modal('show')
                    .find('#modalContent')
                    .load($(this).attr('value'));
             //dynamiclly set the header for the modal via title tag
            //document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('title') + '</h4>';
        }
    });
</script>

