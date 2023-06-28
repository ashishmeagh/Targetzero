<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
	use yii\helpers\Url;
    use yii\bootstrap\Modal;
    use kartik\select2\Select2;
    $this->title = 'Users';
    $this->params['breadcrumbs'][] = $this->title;
    //¿Rol system admin?
    $userIsSystemAdmin = Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN;
    $actionsTitle = ($userIsSystemAdmin ? "Edit/Delete" : "Edit");
    $userStatus = app\models\User::getUserStatus();
?>
<style type="text/css">
    .ms-drop input[type="radio"], .ms-drop input[type="checkbox"] {
    position: unset !important;
}
.ms-choice>div.icon-caret {
    margin-top: -5px !important;
}

.ms-parent.form-control {
    border-bottom: 0px !important;
}
.ms-drop.bottom{
    width: 100%;
}
.fg-line:not([class*=has-]):after {
    display:none;
}
.ms-search {
    position: static;
    top: 0;
}
.ms-drop.bottom {
    height: 250px;
    overflow: auto;
}
.ms-drop ul {
    overflow: unset !important;
}
.ms-choice>div.icon-close:before {

top: 45% !important;

}

</style>
<div class="LockOn">
    <div id="coverScreen">
    </div>
</div>
<div class="user-index">

    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>

    <div class="block-header">
        <h2>
            <?= Html::encode($this->title) ?>
        </h2>
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
             <strong> <?= Yii::$app->session->getFlash('success') ?></strong>
            
        </div>
    <?php else: ?>
        <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
           <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('error') ?></strong>
        </div>
	<?php else: ?>
        <?php if(Yii::$app->session->hasFlash('jobsite')): ?>
            <div class="alert alert-danger alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
           <strong style="font-size: 15px;"> <?= Yii::$app->session->getFlash('jobsite') ?></strong>
        </div>
	<?php endif; ?> 
    <?php endif; ?>
    <?php endif; ?>
        <ul class="actions">
            <li>
                <?= Html::a('<i class="md md-add"></i>', ['create'], ['class' => '','title'=>'Add New User',
                                    'data-toggle'=>'tooltip', 'data-placement'=>'bottom']) ?>
            </li>
            <li>
                <?= Html::a('<i class="md md-file-download"></i>',['#'], ['class' => '','id'=>'downloadLink','title'=>'Download Users',
                                    'data-toggle'=>'tooltip', 'data-placement'=>'bottom']) ?>
            </li>
        </ul>
    </div>
	<?php 
    Modal::begin([
        'header'=>'<h5 class="modal-title" id="exampleModalLongTitle">Choose Jobsites</h5>',
        'size' => 'modal-dialog',
        'id' => 'user-data-modal'
    ]);
    
    echo "<div class='modelContent'>".
        Html::beginForm(['user/users-template'], 'post', ['enctype' => 'multipart/form-data'])?>
        <?= Html::dropDownList('jobsites', [], ArrayHelper::map($data_query, 'id', 'jobsite'), [
           'multiple' => 'multiple', 'size'=> false, 'class' => 'form-control', 'id'=> 'jobsite-select'
           
        ]) ?>
    <div class="modal-footer"  style="margin-top: 5%;">
    <?= Html::submitButton('Export', ['class' => 'btn-primary btn pull-right', 'id'=>'user-data-exp']) ?>
    </div>
    <?=Html::endForm()
   ."</div>";
    Modal::end();
    ?>

    <div class="card">
        <div class="card-body p-t-15">

			<?php $form = ActiveForm::begin([
					'method' => 'get',
			]); ?>

			<div class="row">
				<div class="col-sm-3 col-sm-offset-8 text-right">
					<?= $form->field($searchModel, 'all_search')->textInput(['placeholder' => 'Find by ID, name, role or contractor'])->label(false) ?>
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
			'format' => 'raw',
                        'label' => 'Status',
                        'contentOptions' => [ 'class' => 'index-status'],
						'value' => function( $data ){
							if( Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_CLIENT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_TRADE_PARTNER){
								return Html::dropDownList(
									'status', 
									$data->is_active, 
									ArrayHelper::map(app\models\User::getUserStatus(),'id', 'name'), [
										'onchange' => 'changeDropdownStatus('.$data->id.', $(this).val());',
									]
								);
							}
                                                        else{
								return $data->getStatusName();
							}  
						/*	if($data->creator_id == Yii::$app->session->get('user.id')){
								return Html::dropDownList(
									'status', 
									$data->app_case_status_id, 
									ArrayHelper::map(app\models\AppCaseStatus::find()->asArray()->all(), 'id', 'status'), [
										'onchange' => 'changeDropdownStatus('.$data->id.', $(this).val());',
									]
								);
							}else{
								return $data->getStatusName();
							}  */
						},
						 'filter' => array('1' => 'Active', '0' => 'Inactive'),
                    ],
                                                        
                  //[
                  //      'attribute' => 'is_active',
                    //    'format' => 'html',
                        
                        
                //        'value' => function($data)
                 //       {
                 //           if($data->is_active == true)
                 //           {
                 //               return Html::tag('i', '', ['class' => 'md md-check is-active']);
                 //           }
                 //           else
                 //           {
                 //               return Html::tag('i', '', ['class' => 'md md-close is-active']);
                 //           }
                 //       },
                 //       'contentOptions'=>['class'=>'active-column'],
                 //       'filter' => array('1' => 'Active', '0' => 'Inactive'),
                   //],
                    [
                        'attribute' => 'sop',
                        'value' => function($data)
                        {
                            if($data->sop == 1)
                                return 'Yes' ;
                            else
                                return 'No' ;
                        },
                        'label' => 'SOP',
                    ],
                    [
                        'attribute' => 'employee_number',
                        'value' => function($data)
                        {
                            return $data->employee_number ;
                        },
                        'label' => 'Emp. ID',
                    ],
                    [
                        'attribute' => 'first_name',
                        'format' => 'raw',
                        'value' => function($data)
                        {
                            return Html::a( $data->first_name.' '.$data->last_name, ['/user/view?id='.$data->id],['data-pjax' => 0,'target' => '_blank']);
                        },
                        'label' => 'Name',
                    ],
//                    [
//                        'attribute' => 'fullName',
//                        'format' => 'html',
//                        'value' => function($data)
//                        {
//                            return Html::a( $data->fullName, ['/user/view?id='.$data->id]);
//                        },
//                        'label' => 'Name',
//                    ],
                    [
                        'attribute' => 'role_id',
                        'value' => 'role.role',
                        'label' => 'Role',
                    ],
                    [
                        'attribute' => 'contractor_id',
                        'value' => 'contractor.contractor',
                        'label' => 'Contractor',
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
                            if( (Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL)
                                || $data->creator_id == Yii::$app->session->get('user.id')){
                               if(($data->role_id == ROLE_SYSTEM_ADMIN)  && (Yii::$app->session->get('user.role_id') == ROLE_ADMIN)){
                                     $returned = "";
                                    
                                }else if($data->role_id == ROLE_WT_CRAFTSMEN){
                                    $returned = Html::a( Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] ), [ '/user/updatecraftmen?id='.$data->id ],['data-pjax' => 0,'target' => '_blank']);                                
                                }else{
                                    $returned = Html::a( Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] ), [ '/user/update?id='.$data->id ],['data-pjax' => 0,'target' => '_blank']);                                
                                }

                            if((Yii::$app->session->get('user.id') == 13101) || (Yii::$app->session->get('user.id') == 31980) || (Yii::$app->session->get('user.id') == 151004)) {
                                  $returned .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                  $returned .= Html::a( Html::tag( 'i', '', [ 'class' => 'md-border-color view-case' ] ), [ '/user/splupdate?id='.$data->id ]);
                                }

                                //Sólo permitir borrar físicamente registros a rol system admin.
                                //¿Rol system admin?
                             if(Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN){
                                  $returned .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                  $returned .= Html::a( Html::tag( 'i', '', [ 'class' => 'md-delete view-case' ] ), [ '/user/delete?id='.$data->id ]);
                                }

                                return $returned;
                              } else {
                                return Html::tag( 'i', '', [ 'class' => 'md-mode-edit view-case' ] );
                            }
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<style>
    .loader {
        border: 10px solid #f3f3f3; /* Light grey */
        border-top: 10px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        text-align: center;
        margin: auto;
        padding-bottom: 15px;
        }
    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
    .LockOn {
    display: block;
    visibility: visible;
    position: absolute;
    z-index: 999;
    top: 0px;
    left: 0px;
    width: 105%;
    height: 105%;
    background-color:white;
    vertical-align:bottom;
    padding-top: 20%; 
    filter: alpha(opacity=75); 
    opacity: 0.75; 
    font-size:large;
    color:blue;
    font-style:italic;
    font-weight:400;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
}
</style>
<link href="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.js"></script>
<script>
        var jquery = $.noConflict(true);
        jquery('#jobsite-select').multipleSelect({
            filter : true,
            placeholder : 'Select Jobsites',
            showClear: true
        });

        var setCookie = function(name, value, expiracy) {
        var exdate = new Date();
        exdate.setTime(exdate.getTime() + expiracy * 1000);
        var c_value = encodeURI(value) + ((expiracy == null) ? "" : "; expires=" + exdate.toUTCString());
        document.cookie = name + "=" + c_value + '; path=/';
        };

        var getCookie = function(name) {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++) {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            
            if (x == name) {
            return y ? decodeURI(y.replace(/\+/g, ' ')) : y; 
            }
        }
    };

        var downloadTimeout;
        var checkDownloadCookie = function() {
        if (getCookie("downloadStarted") == 1) {
            setCookie("downloadStarted", "false", 100); 
            jquery(".LockOn").hide();
            jquery('#jobsite-select').multipleSelect('uncheckAll');
        } else {
            downloadTimeout = setTimeout(checkDownloadCookie, 1000); 
        }
};
</script>
<script language="javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<script>
$(window).on('load', function () {
        $(".LockOn").hide();
});
    	var changeDropdownStatus = function(user_id, status_id){
		executeAjax
		(
			"<?= Yii::$app->urlManager->createUrl('ajax/set-status-user?user_id=') ?>"+user_id+"&status_id="+status_id
		).done(function(r){
			if(r){
				notify( "success", "Status changed successfully!");
				$.pjax.reload({container:'#w1'});
			}else{
				notify( "danger", "There was a problem changing the status!");
			}
		}).fail(function(x){
			notify( "danger", "There was a problem changing the status!");
		});
		return false;
	}
		

    $('#user-data-exp').click(function() {
        $('#user-data-modal').modal('hide');
        $(".LockOn").show();
        $("#coverScreen").addClass('loader');
        setCookie('downloadStarted', 0, 100); 
        setTimeout(checkDownloadCookie, 1000); 
});
    $('#downloadLink').click(function(event) {
        event.preventDefault();
        $('#user-data-modal').modal('show');
        
   
});
</script>