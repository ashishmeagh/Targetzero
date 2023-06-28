<?php


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url

?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css"/>
<link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />

 <style>
    #dvjobsites{
        margin-bottom: 40px;
    }
    #add-jobsites{
        margin-top: 15px;
    }
    
 </style>

<?php
$id = $model->id;
?>
 
<div id="dvjobsites" class="card-body card-padding" tabindex="0" >
    <p><?= $model->contractor ?></p>
<hr>
        
    <table  id="example" class="display" style="width:100%">
    <thead>
            <tr>
                
                <th>Id</th>
                <th>Unassigned Jobsites</th>
                
            </tr>
        </thead>
        
    </table>
    <?= Html::button( 'Add', [ 'class' => 'btn btn-primary pull-right', 'id' => 'add-jobsites', 'style' => 'right: 100px' ]  ) ?>
</div>

<script language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
   $(document).ready(function () {
    
    var contractor_id = "<?php echo $id; ?>";
    var JobsitesDataTable = GetJobsitesData(contractor_id);

    $('#example tbody').on('click', 'tr', function () {
        $(this).toggleClass('selected');
    });

    $("#add-jobsites").click(function(){
        AddJobsites(JobsitesDataTable);
        $(this).addClass("disabled");
   });
    
});

function GetJobsitesData(contractor_id){

    var JobsitesData = $('#example').DataTable({
                    processing: true,
                    serverSide: false,
                    scrollY:        200,
                    "oLanguage": {
                            "sEmptyTable": "This Contractor is already assigned to all jobsites that you have access to."
                        },
                    deferRender:    true,
                    scroller:       true,
                    "bPaginate": false,
                    "ajax": {
                                "url": "/ajax/get-jobsites-by-user?cid="+contractor_id,
                                "dataSrc":"",
                                "type": "GET",
                                "datatype": 'json',
                            },
                    'columnDefs': [{
                                'targets': 0,
                                'searchable': false,
                                'orderable': false,
                                'checkboxes': {
                                'selectRow': true
                                }
                            }],
                    'select': {
                        'style': 'multi'
                    },
                    'order': [[1, 'asc']],
                    columns: [
                        { 'data': 'id' },
                        { 'data': 'jobsite' }]
    });
    return JobsitesData;
}

function AddJobsites(JobsitesDataTable){

    var contractor_id = "<?php echo $id; ?>";
    var rowdata = JobsitesDataTable.rows('.selected').data();     
         var jobsites_str = [];
              for (var i = 0; i < rowdata.length; i++) {
                jobsites_str.push(rowdata[i]['id']);
            }
        
        executeAjax
                    (
                        "<?= Yii::$app->urlManager->createUrl('ajax/add-jobsite?cid=') ?>" +contractor_id+ "<?= '&jobsites=' ?>" + jobsites_str
                    ).done(function(r) {
                        window.location.replace("/contractor/view?id="+contractor_id); 
                    });
}
</script>
