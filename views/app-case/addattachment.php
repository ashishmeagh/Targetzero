<?php

use app\models\CausationFactor;
use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
?>

<style>
.filepond--root {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial,
        sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
}
.filepond--root{
  font-size: 20px;
}
.filepond--credits{
    display: none;
}
.view-field-attachment{
    width: 445px;
}
.filepond--root .filepond--drop-label {
    min-height: 4em;
}
.filepond-error{
    color: #f44336;
    font-weight: bold;
}
</style>

<div class="card">

    <div class="card-header">
       <label class="checkbox checkbox-inline m-r-20"  style="line-height: 100%; font-size: 17px; font-weight: 400;">
        <input type="checkbox" value="1" class="viewattachment-checkbox"
        name="view_attachment"  id="view_attachment" onchange="onChangeviewAttachment(this)" <?php echo ($Isattachmentenable == 1 ) ? "" : " disabled='disabled'" ?> >
        <i class="input-helper"></i>Add Attachment
    </label>
  </div>
    <div id="viewattachment" class="card-body card-padding" tabindex="0" style="display:none; padding-top: 2%;" >
     <?php $form = ActiveForm::begin([
    'options' => [
      'id' => 'viewattachmentform',
        'enctype' => "multipart/form-data"        
    ],
]);?>

<div class="row">
        <div class="col-sm-6">
            <div class="form-group view-field-attachment required fg-line">
                    <div class="filepond-error"></div>
                     <div id="dvfileattachmentinview" style="display: block">
                        <input type="file" class="upload-file filepond" name="attachment[]" multiple >
                    </div>
                     <div class="help-block viewfileattach-error"></div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-sm-6 pull-right">
        <?=Html::submitButton('Upload attachment', ['class' => 'btn btn-primary pull-right', 'id' => 'attachment-form-submit-button'])?>
    </div>
</div>
<?php ActiveForm::end();?>

</div>

</div>
<script src="https://unpkg.com/filepond-polyfill/dist/filepond-polyfill.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script>
// Register plugins
FilePond.registerPlugin(
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType
    );

    // Set FilePond options
FilePond.setOptions({
maxTotalFileSize: '20MB',
labelMaxTotalFileSizeExceeded : '',
allowFileTypeValidation: true,
acceptedFileTypes: ['image/jpeg','image/png','application/pdf',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/heic'],
labelFileTypeNotAllowed: 'File of invalid type',
labelMaxTotalFileSize: 'Maximum total file size is {filesize}.',
fileValidateTypeLabelExpectedTypes: 'It supports only {allButLastType} or {lastType}.',
fileValidateTypeLabelExpectedTypesMap: { 'image/jpeg': '.jpg','image/png': '.png',
                                         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':'.xlsx',
                                         'application/pdf':'.pdf',
                                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document':'.docx','image/heic':'.heic' },
fileValidateTypeDetectType: validateType
});

var pond = FilePond.create(
    document.querySelector('.filepond'),{
    acceptedFileTypes: ['image/jpeg','image/png','application/pdf','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/heic'],
        storeAsFile: true,
           required: true 
}
);

var onChangeviewAttachment = function (element) {
       if (element.checked)
        {
            $("#viewattachment").show();

        } else
        {
            $("#viewattachment").hide();
            
        }
    };

var filenames = [];
pond.on('addfile',
    function(error, file){
        if(filenames.includes(file.filename)){
            error = {
                main: 'duplicate',
                sub: 'A file with the same name was already selected.'
  }
}
        if(error){
            handleFileError(error, file);
            filenames.push(file.filename);
        }else{
            filenames.push(file.filename);
            var err = document.querySelector(".filepond-error");
            err.innerHTML = " ";
        } 
    });

pond.on('removefile',
    function(error, file){
        var index = filenames.indexOf(file.filename);
        filenames.splice(index, 1);
    });

function handleFileError(error, file){
    var err = document.querySelector(".filepond-error");
    err.innerHTML = "'"+ file.filename + "', cannot be loaded.</br> " + error.sub;
    pond.removeFile(file);
}

function validateType(source, type) {
      const p = new Promise((resolve, reject) => {
        if (source.name.toLowerCase().indexOf('.heic') !== -1) {
            
          resolve('image/heic')
        } else {
          resolve(type)
        }
      })

      return p
    }
</script>