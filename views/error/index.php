<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use yii\widgets\ActiveForm;
        use yii\helpers\ArrayHelper;
    use yii\helpers\Url;

    $this->title = 'Target Zero';
    AppAsset::register($this);
?>

<div class="main">
               <section id="login" class="bgScrren011">
                  <div class="container">
                    <h1 class="404sderror" style="
                        text-align: center;
                        font-size: 150px;
                        color: #9f9f9f; margin-top: 6%;
                        ">404</h1>
                      <h1 class="accessdeniedheading">Page not found</h1>
                     <p class="accessdescription"></p>
                     <?= Html::button('LOGOUT', ArrayHelper::merge(['onclick'=>"window.location.href = '" . Url::to(['/userlogin/logout']). "';"], ['class' => 'logoutbutton'])); ?>
                  </div>
               </section>
            </div>