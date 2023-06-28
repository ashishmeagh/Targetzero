$(document).ready(function () {
    var contractor_id = "<?php echo $id; ?>";
    
    var table = $('#example').DataTable({
                    processing: true,
                    serverSide: false,
                    scrollY:        200,
                    deferRender:    true,
                    scroller:       true,
                    "ajax": {
                                "url": "/web/ajax/get-jobsites-by-user",
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
                        },'order': [[1, 'asc']],
                    columns: [
                        { 'data': 'id' },
                        { 'data': 'jobsite' },
                        
                    ]
    });
    $('#example tbody').on('click', 'tr', function () {
        $(this).toggleClass('selected');
    });
 
    

   

        
});