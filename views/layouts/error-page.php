<?php
    use yii\helpers\Html;
    use app\assets\AppAsset;

    AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="stylesheet" href="../css/login-css.css">
    </head>

  <body class="bgScrren11">
    <?php $this->beginBody() ?>
      <div class="Container-fluid">
         <div class="topnavScreen01">
                <?= Html::img( '@web/img/logo.png', ['class'=>'targetzerologoleftside']) ?>
                <?= Html::img( '@web/img/Group88.png', ['class'=>'whitingturnerlogorightside']) ?>
                  </div>
      </div>

                    <?= $content ?>

<footer class="footer">
<div class="container-fluid">
<p class="pull-right">&copy; Whiting-Turner <?= date('Y') ?></p>
</div>
</footer>
    <?php $this->endBody() ?>
    </body>

</html>

<?php $this->endPage() ?>
