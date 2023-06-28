<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\Breadcrumbs;
    use yii\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\helpers\security;
    use yii\bootstrap\Modal;

    $this->title = $model->contractor;
    $this->params[ 'breadcrumbs' ][ ] = [
        'label' => 'Contractors',
        'url'   => [ 'index' ]
    ];
    $this->params[ 'breadcrumbs' ][ ] = $model->contractor;
?>
<style>
    .showModalButton{
        font-style: normal;
    }
    .contractor-jobsites{
        overflow: auto;
        overflow-y: show;
        Height:306px;
    }
</style>



<div class="contractor-view">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>Contractor data</h2>
        <?php if(Yii::$app->session->hasFlash('jobsite')): ?>
            <div class="alert alert-success alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
           <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('jobsite') ?></strong>
    </div>
        <?php endif; ?> 
    </div>
<?php
Modal::begin([
    'header' => '<h4 style="color:white;">Please select a jobsite below</h4>',
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
        <h2 class="p-b-0"><?= $model->contractor ?></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">

            <table class="table">

                <tbody>

                <tr>
                    <th><?= $model->getAttributeLabel( 'is_active' ) ?></th>
                    <td><?= ( $model->is_active ) ? Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] ) : Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] ) ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'created' ) ?></th>
                    <td><?= date( "M d, Y - h:i:s A", strtotime( $model->created ) ) . " (CST)"  ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'updated' ) ?></th>
                    <td><?= date( "M d, Y - h:i:s A", strtotime( $model->updated ) ) . " (CST)"  ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'contractor' ) ?></th>
                    <td><?= $model->contractor ?></td>
                </tr>
                <tr>
                    <th><?= $model->getAttributeLabel( 'vendor_number' ) ?></th>
                    <td><?= $model->vendor_number ?></td>
                </tr>

                <tr>
                    <th><?= $model->getAttributeLabel( 'address' ) ?></th>
                    <td><?= $model->address ?></td>
                </tr>

                </tbody>

            </table>

            <div class="divider"></div>
            <div class="row" id="contractor-jobsite">
            <?= Html::a( Html::tag( 'i', 'Add To Jobsites', [ 'class' => 'btn btn-primary showModalButton pull-right','value'=>'/contractor/add-jobsite?id='.$model->id, 'style'=>'margin-right:12px;' ] )); ?>
                <div class="col-sm-12">
                    <div>
                        <div class="block-header p-0 m-0">
                            <h2 class="p-0 m-0">Assigned jobsites</h2>
                            <hr>
                            <span class="text-danger"> Note: "Removal of Jobsites from the contractor's profile is restricted , Please contact System Admin (CMIC Helpdesk/Sammy Torres)".</span>
                            <?php \yii\widgets\Pjax::begin(); ?>
                            <?php $form = ActiveForm::begin( [
                        'method' => 'get'
                    ] ); ?>
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-7 text-right ">
                            <?= $form->field( $contrJobsitesearchModel, 'jobsite' )->textInput( [ 'placeholder' => 'Find Jobsite ...'] )->label( FALSE ) ?>
                        </div>
                        <div class="col-sm-1 p-l-0 text-right pull-right">
                            <?= Html::submitButton( '<i class="md md-search"></i>', [
                                'class' => 'btn btn-primary waves-effect',
                                'style' => 'margin-top: 4px;'
                            ] ) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="contractor-jobsites" >
                    <?= GridView::widget( [
                        'dataProvider' => $contractorDataProvider,
                        'tableOptions' => [ 'class' => 'table' ],
                        'columns'      => [
                            [
                                'attribute' => 'jobsite',
                                'format'    => 'html',
                                'value'     => function ( $data )
                                    {
                                    return Html::a( $data->jobsite );
                                },
                                'label'     => 'Jobsites',
                            ],[
                                'attribute' => 'actions',
                            'format' => 'raw',
                            'label' => 'Delete',
                            'visible' => (Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN) ? true : false,
                            'contentOptions' => [ 'class' => 'table-action-button' ],
                            'value' => function($data){   

                                if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN){
                                    return Html::a( Html::tag( 'i', '', [ 'class' => 'md-delete view-case deleteJobsite','value'=>$data->id, 'style'=>'margin-left:10px;' ] ));
                                }
                            },
                            ]
                        ],
                    ] ); ?>
                    </div>

                    <?php \yii\widgets\Pjax::end(); ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="card">
        <div class="card-body table-responsive card-padding " tabindex="0" style="overflow: hidden; outline: none;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="block-header p-l-0 m-b-10">
                        <h2 class="p-0"><?= strtoupper( "Contractor users" ); ?></h2>
                    </div>

                    <?php \yii\widgets\Pjax::begin(); ?>

                    <?php $form = ActiveForm::begin( [
                        'method' => 'get'
                    ] ); ?>
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-7 text-right ">
                            <?= $form->field( $searchModel, 'all_search' )->textInput( [ 'placeholder' => 'Find User ...'] )->label( FALSE ) ?>
                        </div>
                        <div class="col-sm-1 p-l-0 text-right pull-right">
                            <?= Html::submitButton( '<i class="md md-search"></i>', [
                                'class' => 'btn btn-primary waves-effect',
                                'style' => 'margin-top: 4px;'
                            ] ) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?= GridView::widget( [
                        'dataProvider' => $userDataProvider,
                        'tableOptions' => [ 'class' => 'table' ],
                        'columns'      => [
                            [
                                'attribute'      => 'is_active',
                                'format'         => 'html',
                                'value'          => function ( $data )
                                {
                                    if ( $data->is_active == TRUE )
                                    {
                                        return Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] );
                                    }
                                    else
                                    {
                                        return Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] );
                                    }
                                },
                                'contentOptions' => [ 'class' => 'active-column' ],
                                'filter'         => array(
                                    '1' => 'Active',
                                    '0' => 'Inactive'
                                ),
                            ],
                            [
                                'attribute'      => 'employee_number',
                                'label'          => 'Emp. ID',
                                'contentOptions' => [ 'class' => 'contractor_employee_number' ],
                            ],
                            [
                                'attribute' => 'first_name',
                                'format'    => 'html',
                                'value'     => function ( $data )
                                {
                                    return Html::a( $data->first_name . ' ' . $data->last_name, [ '/user/update?id=' . $data->id ] );
                                },
                                'label'     => 'Name',
                            ],
                            [
                                'attribute' => 'role_id',
                                'value'     => 'role.role',
                                'label'     => 'Role',
                            ],
                        ],
                    ] ); ?>
                    
                    <?php echo Html::a( 'Add user', [ '/user/create'],['class'=>'btn btn-primary btn-default waves-effect waves-button pull-right create-new-contractor-user'], ['target'=>'_blank']);?>
                    <!-- <a data-toggle="modal" href="#create-new-contractor-user" class="btn btn-primary btn-default waves-effect waves-button pull-right create-new-contractor-user">Add user</a> -->
                    <?php \yii\widgets\Pjax::end(); ?>
                </div>


            </div>
        </div>
    </div>
</div>
<script language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">

      $(document).on('click', '.showModalButton', function(){
        
        if ($('#modal').data('bs.modal').isShown) {
            $('#modal').find('#modalContent')
                    .load($(this).attr('value'));
        } else {
            //if modal isn't open; open it and load content
            $('#modal').modal('show')
                    .find('#modalContent')
                    .load($(this).attr('value'));
        }
        
        
    });

    $(".deleteJobsite").click(function(){
        var jobsiteId = $(this).attr('value');
        var contractorId = <?= $model->id?>;
        var contractorName = "<?= $model->contractor?>";
        var contrJobsiteTable = $(".contractor-jobsites tbody");
        var jobsiteName;
        var trows = contrJobsiteTable.children("tr");

        $.each(trows, function (index, row) {
            var rowDataKey=$(row).attr('data-key');
            
            if(jobsiteId == rowDataKey){
                jobsiteName = $(row).children('td:first').children('a'). text();
            }
        });
        swal({
            html:true,
            title: "Are you sure?",
            text: "You want to delete the contractor <br><b>" +contractorName+ "</b><br> from this jobsite <br><b>"+jobsiteName+"</b>.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF6319",
            confirmButtonText: "Yes",
            closeOnConfirm: false
            }, function(isConfirm){
                    if (isConfirm){
                        $('.confirm').addClass("disabled");
                        $('.cancel').addClass("disabled");
                        executeAjax
                                (
                                    "<?= Yii::$app->urlManager->createUrl('ajax/delete-jobsite?cid=') ?>" +contractorId+ "<?= '&jid=' ?>" + jobsiteId
                                ).done(function(r) {
                                    location.reload();
                                });
                    }
            }); 
   });
</script>