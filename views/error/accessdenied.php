<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;


    $this->title = 'Target Zero';
    AppAsset::register($this);
?>            
<div class="main">
               <section id="login" class="bgScrren011">
                  <div class="container">
                    <?= Html::img( '@web/img/accessdenied.png', ['class'=>'accessdeniedicon']) ?>
                      <h1 class="accessdeniedheading">Access Denied</h1>
                     <p class="accessdescription">You don't have access to this website, <br>Please contact your Jobsite Administrator</p>
                     <?= Html::button('LOGOUT', ArrayHelper::merge(['onclick'=>"window.location.href = '" . Url::to(['/userlogin/logout']). "';"], ['class' => 'logoutbutton'])); ?>
                    </div>
               </section>
            </div>
