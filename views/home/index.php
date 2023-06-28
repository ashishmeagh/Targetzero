<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;

    $this->title = 'Target Zero';
    AppAsset::register($this);
?>

   <div class="row">
<div class="columnScreen01">
   <div class="cardScreen01">
      <h5 class="fg">Are you a Whiting-Turner Employee?</h5>
      <div class="flex-container">
         <div style="flex-grow: 6">
            <?= Html::button('Yes', ArrayHelper::merge(['onclick'=>"window.location.href = '" . Url::to(['/saml/login']). "';"], ['class' => 'buttonyes bothbuttons'])); ?>
        </div>
         <div style="flex-grow: 1">
            <div class="vl"></div>
         </div>
         <div style="flex-grow: 6">
            <?= Html::button('No', ArrayHelper::merge(['onclick'=>"window.location.href = '" . Url::to(['/userlogin']). "';"], ['class' => 'buttonyno bothbuttons'])); ?>
         </div>
      </div>
   </div>
</div>
</div>
