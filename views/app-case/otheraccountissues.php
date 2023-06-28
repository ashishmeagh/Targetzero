<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\BaseStringHelper;
	use yii\helpers\ArrayHelper;
	use yii\widgets\ActiveForm;
	
	use app\components\sqlRoleBuilder;
	use app\components\HelloWidget;

    $this->title = $title;
    $this->params[ 'breadcrumbs' ][ ] = $title;

$UrlParams = Yii::$app->request->queryParams;
$ownerValue ='';
$ownerId ='';
$contractorValue ='';
$contractorId ='';
$jobsiteValue ='';
$jobsiteId ='';
if(isset($UrlParams["owner-ads"])){
$ownerValue = $UrlParams["owner-ads"];
$ownerId = $UrlParams["AppCase"]["creator_id"];	
}
//var_dump($ownerValue); exit();
if(isset($UrlParams["contractor-ads"])){
$contractorValue = $UrlParams["contractor-ads"];
$contractorId = $UrlParams["AppCase"]["contractor_id"];	
}
if(isset($UrlParams["jobsite-ads"])){
$jobsiteValue = $UrlParams["jobsite-ads"];
$jobsiteId = $UrlParams["AppCase"]["jobsite_id"];	
}

$loggedInUser = Yii::$app->session->get("user.id");
 $MergeEmployee_query = "select u.employee_number, mu.child_userid from [dbo].[user] u inner join [dbo].[merge_users] mu on mu.child_userid = u.id where mu.status = 0 and mu.parent_userid = ".$loggedInUser;
     

$MergeEmployee = Yii::$app->db->createCommand("$MergeEmployee_query")->queryAll();
//$selectedemployee = [];
$MergeEmployee_Array = [];
foreach ($MergeEmployee as $key => $value) {

	$MergeEmployee_Array[$value ["child_userid"]] = $value ["employee_number"];
}


?>

<style type="text/css">
.issuedisabled{
     pointer-events: none; 
  background-color: #edf1f2;
}

	.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
.circleloader {
    background: url(./img/circleloader.gif);
    background-repeat: no-repeat;
    background-position: 85% 76%;
}
</style>
<!-- Safety Code Modal Selection -->
<div class="modal" id="reassign-user-creator-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reassign</h4>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-md-12"><p>Select new owner</p></div>
					<div id="reassign-user-creator-dropdown-container" class="col-sm-12"></div>
				</div>
			</div>
            <div id="btn-reassign-user-creator-container" class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Safety Code Modal Selection -->

<div class="app-case-index">
    <?= Breadcrumbs::widget( [
        'links' => isset( $this->params[ 'breadcrumbs' ] ) ? $this->params[ 'breadcrumbs' ] : [ ],
    ] ) ?>

    <div class="card" id="app-case-index">
        <div class="card-body p-t-15">
		
			<?php $form = ActiveForm::begin([
					'method' => 'get',
			]); ?>
			
			<div class="row">
				<div class="col-sm-3 col-sm-offset-5 text-right">
					<?= 
					Html::a('Advanced search', null, [
						'onclick' => '$(".colapsable").slideToggle(); return false;',
						'style' => '  cursor: pointer; display: block; margin: 8px 0 0 0;',
					]) 
					?>
				</div>
				<div class="col-sm-3 text-right">
					<?= $form->field($searchModel, 'additional_information')->textInput(['placeholder' => 'Description/Additional Information ...'])->label(false) ?>
				</div>
				<div class="col-sm-1">
					<?= Html::submitButton('<i class="md md-search"></i>', ['class' => 'btn btn-primary waves-effect', 'style' => 'margin-top: 4px;']) ?>
				</div>
			</div>			
			<div class="row colapsable" style="display: none;">
                <div class="col-sm-2 col-sm-offset-1 hidden">
                    <div class="form-group field-appcase-app_case_status_id fg-line has-success ">
                <label class="control-label" for="appcase-otheraccount-emp">Employee Number</label>
                  <?=Html::dropDownList('otheraccount-employeeid',$selectedemployee, $MergeEmployee_Array,  ['prompt' => '-Choose a Employee Number-',  'id' => 'employeeid', 'class' => 'form-control'])?>

                <div class="help-block"></div>
                </div>
                </div>

				<div class="col-sm-2 col-sm-offset-1">
					<?= $form->field($searchModel, 'app_case_status_id')->dropDownList(
						ArrayHelper::map( app\models\AppCaseStatus::find()->asArray()->all(), 'id', 'status'),
						['prompt' => '-Choose a status-']
					) ?>
				</div>
				<div class="col-sm-2 hidden">
					<div class="form-group field-appcase-creator_id fg-line">
					<label class="control-label" for="appcase-creator_id">Owner </label>
					<input type="text" class="form-control selectpicker" placeholder="Search by min 3char of owner" name="owner-ads" id="owner-ads" value=<?= '"'.$ownerValue.'"' ?>/>   
                    <input type="hidden"  name="AppCase[creator_id]" id="appcase-creator_id" value=<?= $ownerId ?> >
					<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-2">
                    <div class="form-group field-appcase-contractor_id fg-line">
					<label class="control-label" for="appcase-contractor_id">Contractor</label>
					<input type="text" class="form-control selectpicker" placeholder="Search by min 3char of contractor" name="contractor-ads" id="contractor-ads" value=<?= '"'.$contractorValue.'"' ?>/>   
                    <input type="hidden" name="AppCase[contractor_id]" id="appcase-contractor_id" value=<?= $contractorId ?> >
					<div class="help-block"></div>
					</div>

				</div>
				<div class="col-sm-2">
					<div class="form-group field-appcase-jobsite_id fg-line">
                    <label class="control-label" for="appcase-jobsite_id">Jobsite</label>

					<input type="text" class="form-control selectpicker" placeholder="Search by min 3char of jobsite" name="jobsite-ads" id="jobsite-ads" value=<?= '"'.$jobsiteValue.'"' ?>/>   
                    <input type="hidden" id="appcase-jobsite_id" name="AppCase[jobsite_id]" value=<?= $jobsiteId ?> >

					<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-2">				
					<?= $form->field($searchModel, 'app_case_type_id')->dropDownList(
						ArrayHelper::map( app\models\AppCaseType::find()->asArray()->all(), 'id', 'type'),
						['prompt' => '-Choose a Type-']
					) ?>
				</div>
			</div>			
			
			<?php ActiveForm::end(); ?>

			<?php 
			if( Yii::$app->session->get('user.role_id') == ROLE_WT_PERSONNEL || Yii::$app->session->get('user.role_id') == ROLE_WT_SAFETY_PERSONNEL ){
				$visible_colum = false;
			}else{
				$visible_colum = true;
			}
			?>
			
			<?php \yii\widgets\Pjax::begin(); ?>
			
            <?= GridView::widget( [
                'dataProvider' => $dataProvider,
                'filterModel'  => null,
                'tableOptions' => [ 'class' => 'table table-hover', 'id' => 'table-app-case' ],
                'summary'      => false,
                'formatter' => [
			        'class' => 'yii\i18n\Formatter',
			        'timeZone' => 'America/Cayman'
			    ],
                'columns'      => [
                    [
                        'attribute' => 'is_active',
                        'format' => 'html',
                        'contentOptions' => [ 'class' => 'active-column' ],
                        'value' => function($data){
                            if ($data->is_active == TRUE){
                                return Html::tag( 'i', '', [ 'class' => 'md md-check is-active' ] );
                            }
                            else{
                                return Html::tag( 'i', '', [ 'class' => 'md md-close is-active' ] );
                            }
                        },
						'visible' => $visible_colum,
						'filter' => array('1' => 'Active', '0' => 'In Active'),
                    ],
                    [
                        // data-trigger="hover" data-toggle="popover" data-placement="top" data-content="" title="" data-original-title=""
                        'attribute' => 'additional_information',
                        'header' => 'Description/Additional Information',
                        'format' => 'raw',
                        'contentOptions' => [ 'class' => 'truncate-container additional_information'],
                        'value' => function ( $data )
                        {
                            return Html::a( $data->additional_information, [ '/app-case/other-account-issues-view?id='.$data->id], ['data-pjax' => 0,'target' => '_blank', 'class' => 'truncate' ] );
                        },
                    ],
					[
                        'attribute' => 'app_case_status_id',
						'format' => 'raw',
                        'label' => 'Status',
                        'contentOptions' => [ 'class' => 'index-status' ],
                        'value' => function( $data ){
                            return Html::dropDownList(
                                    'status', 
                                    $data->app_case_status_id, 
                                    ArrayHelper::map(app\models\AppCaseStatus::find()->asArray()->all(), 'id', 'status'), [
                                        'onchange' => 'changeDropdownStatus('.$data->id.', $(this).val());',
                                    ]
                                );
                        },
						'filter' => array('1' => 'Open', '2' => 'Close', '3' => 'Overdue'),
                    ],
					//[
					//	'attribute' => 'app_case_priority_id',
					//	'value'     => 'appCasePriority.priority',
					//	'label'     => 'Priority',
					//	'contentOptions' => [ 'class' => 'app-case-priority-id' ],
					//],
                    [
                        'attribute' => 'creator_id',
                        'value'     => function ($model) {
                            return $model->creator->first_name ." ". $model->creator->last_name;
                        },
                        'label'     => 'Owner',
                        'contentOptions' => [ 'class' => 'owner' ],
                    ],
                    [
                        'attribute' => 'contractor_id',
                        'value'     => 'contractor.contractor',
                        'label'     => 'Contractor',
                        'contentOptions' => [ 'class' => 'contractor' ],
                    ],
                    [
                        'attribute' => 'affected_user_id',
                        'value'     => function ($model) {
                            return $model->affectedUser->first_name ." ". $model->affectedUser->last_name;
                        },
                        'label'     => 'Affected Employee',
                        'contentOptions' => [ 'class' => 'affected-employee' ],
                    ],
					[
                        'attribute' => 'jobsite_id',
                        'value'     => 'jobsite.jobsite',
                        'label'     => 'Jobsite',
                        'contentOptions' => [ 'class' => 'jobsite' ],
                    ],
                    [
                        'attribute' => 'app_case_type_id',
                        'value'     => 'appCaseType.type',
                        'label'     => 'Type',
                        'contentOptions' => [ 'class' => 'issue-type' ],
                    ],
                    [
                        'attribute'      => 'created',
                        'format'         => [
                            'date',
                            'php:M d, Y'
                        ],
                        'contentOptions' => [ 'class' => 'date-column' ],
                    ],
                    [
                        'attribute'      => 'updated',
                        'format'         => [
                            'date',
                            'php:M d, Y'
                        ],
                        'contentOptions' => [ 'class' => 'date-column' ],
                    ],
                ],
            ] ); ?>
			
			<?php \yii\widgets\Pjax::end(); ?>
			
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script>

	var showModalToReassignUserCreator = function(app_case_id, creator_id, jobsite_id, current_element_id){
		getUsersByContractorDropdown(
			"<?= Yii::$app->urlManager->createUrl('ajax/get-users-by-jobsite?id=') ?>"+jobsite_id,
			"#reassign-user-creator-modal",
			"#reassign-user-creator-dropdown-container",
			"#btn-reassign-user-creator-container",
			app_case_id,
			current_element_id
		);
		return false;
	}
	
	var reassignUserCreator = function(app_case_id, element){		
		select_creator_id = $('#user-by-contractor-id').val();
		executeAjax
		(
			"<?= Yii::$app->urlManager->createUrl('ajax/set-user-creator-issue?app_case_id=') ?>"+app_case_id+"&creator_id="+select_creator_id
		).done(function(r){
			if(r){
				notify( "success", "Owner changed successfully!");
				$.pjax.reload({container:'#w1'});
			}else{
				swal("Error!", "Something is wrong, please try again later.", "error");
			}
		}).fail(function(x){
			swal("Error!", "Something is wrong, please try again later.", "error");
		});
		return false;
	}
	
	var changeDropdownStatus = function(app_case_id, status_id){
		executeAjaxGET
		(
			"<?= Yii::$app->urlManager->createUrl('ajax/set-status-other-issue?app_case_id=') ?>"+app_case_id+"&status_id="+status_id
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
        //Create a Auto Search URL
    autoSearchOwnerurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-app-case-owner-ads') ?>";
	 $('#owner-ads').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchOwnerurl,
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:3,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-creator_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#owner-ads').val().length == 0){
         $('#appcase-creator_id').val('');
        }        
    })
    .focus(function() {
    if($('#owner-ads').val().length == 0){
         $('#appcase-creator_id').val('');
        }        
    });

      autoSearchcontractorerurl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-other-account-contractor-as') ?>";
	 $('#contractor-ads').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchcontractorerurl,
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:3,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-contractor_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#contractor-ads').val().length == 0){
         $('#appcase-contractor_id').val('');
        }        
    })
    .focus(function() {
    if($('#contractor-ads').val().length == 0){
         $('#appcase-contractor_id').val('');
        }        
    });

      autoSearchjobsiturl = "<?= Yii::$app->urlManager->createUrl('/ajax/get-other-account-jobsite-as') ?>";
	 $('#jobsite-ads').autocomplete({
        paramName: 'searchkey',
        serviceUrl: autoSearchjobsiturl,
        onSearchStart: function (container) {
                $(this).addClass('circleloader');
        },
        onSearchComplete: function (container) {
                $(this).removeClass('circleloader');
        },
        minChars:3,
        noCache: true,
        triggerSelectOnValidInput: false,
        showNoSuggestionNotice: true,
        onSelect: function (suggestion) {
           $('#appcase-jobsite_id').val(suggestion.data);
        }
    }).blur(function() {
    if($('#jobsite-ads').val().length == 0){
         $('#appcase-jobsite_id').val('');
        }        
    })
    .focus(function() {
    if($('#jobsite-ads').val().length == 0){
         $('#appcase-jobsite_id').val('');
        }        
    });


</script>
