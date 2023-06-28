<?php

use yii\helpers\Url;
?>
<style type="text/css">
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
<div class="user-view">

    <div class="block-header">
        <h2>Generate QR code</h2>
    </div>

    <div class="card">

        <h2 class="p-b-0"></h2>

        <div class="card-body table-responsive card-padding" tabindex="0" style="overflow: hidden; outline: none;">


   <div class="row">
        <div class="col-sm-6" id="qr-code-jobsite">

					<div class="form-group field-appcase-jobsite_id fg-line">
                    <label class="control-label" for="appcase-jobsite_id">Jobsite</label>

					<input type="text" class="form-control selectpicker" placeholder="Search by min 3char of jobsite" name="jobsite-ads" id="jobsite-ads" value= >
                    <input type="hidden" id="appcase-jobsite_id" name="AppCase[jobsite_id]" value= >

					<div class="help-block" id="jobsite_error"></div>
					</div>

					<div class="help-block has-warning hidden" id="jobsite_duplicate"><b><span class="text-warning">Warning:</span>
						<span style="color: #31708f;">"Are you sure you want to generate a new QR code? This will invalidate the current QR code."</span>
					</b></div>
					</div>


 </div>
</div>
 <div class="row">
        <div class="col-sm-12">
        	<button id="Print" style="margin-left: 19px;"class="btn btn-primary pull-center">Publish and download pdf</button>
 </div>
</div>
 <div class="row" style="margin-top: 29px; margin-left: 2%;">
        <div class="col-sm-12">
        	<span id="URL_link" ></span><br/><br/>
        	<canvas id="qr"></canvas>
 </div>
</div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/jquery.autocomplete.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
        <script type="text/javascript">
        	      autoSearchjobsiturl = "<?=Yii::$app->urlManager->createUrl('/ajax/get-all-jobsites')?>";
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
           CheckJobSiteisCreated(suggestion.data);
        }
    }).blur(function() {
    if($('#jobsite-ads').val().length == 0){
         $('#appcase-jobsite_id').val('');
        }
    })
    .focus(function() {
    if($('#jobsite-ads').val().length == 0){
         $('#appcase-jobsite_id').val('');
         $('#jobsite_duplicate').addClass('hidden');
        }
    });

    $('#Print').click(function(){
$('#jobsite_error').empty();
$('.field-appcase-jobsite_id').removeClass('has-error');
   var jobid = $('#appcase-jobsite_id').val();
   var JobsiteName = $('#jobsite-ads').val();
   if(jobid == undefined || isNaN(jobid) || jobid <= 0){
   	$('#jobsite_error').empty().append('Please select the Jobsite');
    $('.field-appcase-jobsite_id').addClass('has-error');
   }else{
   var todaydate = new Date().toISOString();
   var encode = btoa(jobid+"@"+todaydate);
   var GenUrl = "<?php echo Url::base(true); ?>/custom-user/index?en=" + encode;
   var shortenURL = GenShortenURL(GenUrl);
   var DisplayTodayDate = new Date();
  var dd = String(DisplayTodayDate.getDate()).padStart(2, '0');
  var mm = String(DisplayTodayDate.getMonth() + 1).padStart(2, '0'); //January is 0!
  var yyyy = DisplayTodayDate.getFullYear();

  DisplayTodayDate = mm + '/' + dd + '/' + yyyy;

       var data1 = {"url": encodeURI(GenUrl), "jobid" :jobid, "encode" : encode };
       var url = "<?php echo Yii::$app->request->baseUrl; ?>/ajax/generate-url";
            $.ajax({
            type: 'get', 
            url: url,
            data: data1,
            success: function (data) {
              var shortenURL = GenUrl;
              if(data != 0){
                var res = JSON.parse(data);
                shortenURL = res.shorturl;
              }
              
              var qr = new QRious({
                        element: document.getElementById('qr'),
                        value: shortenURL
                      });
                $('#URL_link').empty().append("<b>URL:</b><a href='"+shortenURL +"'a>"+ shortenURL+"</a>")
                var doc = new jsPDF();
                var img = qr.toDataURL();
                var HTML = "<!DOCTYPE html><html><head><title>Target Zero</title></head><body><h1>Target Zero User Registration QR code</h1> <h2>Date - "+ DisplayTodayDate +"</h2> <h2>Jobsite - "+ JobsiteName +"</h2><p style=' line-height: 0.8;'>Please Scan the below QR code or type the below URL in your web browser.</p><p style=' line-height: 0.8;'>Favor the escanear el codigo de abajo o escriba la direccion URL <br/>en su navegador de internet.</p><p>Note: If code does not work, see a WT personnel.  </p><p style=' line-height: 0.8;'>Si no funciona el codigo, favor de reportarlo a una persona de Whiting-Turner.</p><p><b>URL:</b>"+shortenURL+"</p> </body></html>";
                //"https://quickchart.io/qr?text=https://stackoverflow.com/questions";

                  doc.fromHTML(HTML, 5, 5);
                  doc.addImage(img, 'png', 15, 100, 180, 180);
                  doc.save('QRcode.pdf');
            }
          });

   }

});

function CheckJobSiteisCreated(jobid) {
	$('#jobsite_duplicate').addClass('hidden');
	    var data1 = { "jobid" :jobid };
       var url = "<?php echo Yii::$app->request->baseUrl; ?>/ajax/check-jobsite-qrcode";
            $.ajax({
            type: 'get', 
            url: url,
            data: data1,
            success: function (data) {
              
              if(data == true){
               $('#jobsite_duplicate').removeClass('hidden');
              }
              
              
            }
          });

}
function GenShortenURL(rurl) {


}



        </script>

    </div>
</div>
