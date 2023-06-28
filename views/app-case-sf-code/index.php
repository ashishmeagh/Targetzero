<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;
    use yii\widgets\ActiveForm;

    $this->title = 'Safety Codes';
    $this->params[ 'breadcrumbs' ][ ] = $this->title;
?>
<div class="app-case-sf-code-index">

    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode( $this->title ) ?>
        </h2>
        <ul class="actions">
            <li>
                <?= Html::a( '<i class="md md-add"></i>', [ 'create' ], [ 'class' => '' ] ) ?>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body p-t-15">

            <?php $form = ActiveForm::begin( [
                'method' => 'get',
            ] ); ?>

            <div class="row">
                <div class="col-sm-3 col-sm-offset-8 text-right">
                    <?= $form->field( $searchModel, 'all_search' )->textInput( [ 'placeholder' => 'Find Safety Code ...' ] )->label( FALSE ) ?>
                </div>
                <div class="col-sm-1">
                    <?= Html::submitButton( '<i class="md md-search"></i>', [
                        'class' => 'btn btn-primary waves-effect',
                        'style' => 'margin-top: 4px;'
                    ] ) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <?php
                if ( Yii::$app->session->get( 'user.role_id' ) != ROLE_ADMIN )
                {
                    $visible_colum = FALSE;
                }
                else
                {
                    $visible_colum = TRUE;
                }
            ?>

            <?= GridView::widget( [
                'dataProvider' => $dataProvider,
                'filterModel'  => NULL,
                'tableOptions' => [
                    'class' => 'table table-hover',
                    'id'    => 'sf-code-edit'
                ],
                'summary'      => FALSE,
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
                        'attribute'      => 'code',
                        'format'         => 'html',
                        'value'          => function ( $data )
                        {
                            return Html::a( $data->code, [ '/app-case-sf-code/update?id=' . $data->id ] );
                        },
                        'label'          => 'Code',
                        'contentOptions' => [ 'style' => 'width: 160px;' ],
                    ],
                    [
                        'attribute' => 'parent_id',
                        'label'     => 'Parent',
                        'format'    => 'raw',
                        'value'     => function ( $data )
                        {
                            if ( is_numeric( $data->parent_id ) )
                            {
                                $return = $data->getParentCode( $data->parent_id );
                            }
                            else
                            {
                                $return = "-";
                            }
                            return $return;
                        },
                    ],
                    [
                        'attribute'      => 'description',
                        'format'         => 'html',
                        'contentOptions' => [ 'class' => 'truncate-container' ],
                        'value'          => function ( $data )
                        {
                            return Html::tag( 'p', $data->description, [ 'class' => 'truncate' ] );
                        },
                        'label'          => 'Description',
                    ],
                    [
                        'attribute'      => 'created',
                        'format'         => [
                            'date',
                            'php:M d, Y'
                        ],
                        'contentOptions' => [
                            'class' => 'date-column',
                            'style' => 'width: 125px;'
                        ],
                    ],
                    [
                        'attribute'      => 'updated',
                        'format'         => [
                            'date',
                            'php:M d, Y'
                        ],
                        'contentOptions' => [
                            'class' => 'date-column',
                            'style' => 'width: 125px;'
                        ],
                    ],
                    [
                        'attribute'      => 'actions',
                        'format'         => 'raw',
                        'label'          => 'Delete',
                        'value'          => function ( $data )
                        {
                            return Html::a( '<i class="md md-delete view-case"></i>', NULL, [
                                'class'   => 'delete-item',
                                'style'   => 'cursor: pointer;',
                                'onclick' => 'deleteItem(' . $data->id . ');',
                            ] );
                        },
                        'visible'        => $visible_colum,
                        'contentOptions' => [ 'style' => 'width: 50px; text-align: center;' ],
                    ],
                ],
            ] ); ?>
        </div>
    </div>
</div>

<script>

    var deleteItem = function ( item_id )
    {
        swal ( {
            title:             "Are you sure?",
            text:              "You will not be able to recover this item!",
            type:              "warning",
            showCancelButton:  true,
            confirmButtonColor:"#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm:    false
        }, function ( isConfirm )
        {
            if ( isConfirm )
            {
                executeAjax( "<?= Yii::$app->urlManager->createUrl('app-case-sf-code/delete?id=') ?>" + item_id ).done ( function ( r )
                {
                    if ( r )
                    {
                        swal ( "Deleted!", "The item has been deleted.", "success" );
                        $.pjax.reload ( { container:'#w0' } );
                    }
                    else
                    {
                        swal ( "Error!", "This item is in use and cannot be deleted.", "error" );
                    }
                }
                ).fail ( function ( x )
                {
                    swal ( "Error!", "This item is in use and cannot be deleted.", "error" );
                }
            )
            return;
            }
        } );
        return false;
    }

</script>