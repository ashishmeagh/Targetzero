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
        <?php $this->head() ?>
    </head>

    <body class="login-container">
    <?php $this->beginBody() ?>

        <section id="login" class="wrap">
            <section id="content">
                <div class="container-fluid">
                    <?= $content ?>
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <p class="pull-right">&copy; Whiting-Turner <?= date('Y') ?></p>
                    </div>
                </footer>
            </section>
        </section>

        <!-- JS -->
        <?php $this->registerJsFile("@web/js/jquery-2.1.1.min.js" ); ?>
        <?php $this->registerJsFile("@web/vendors/moment/moment.min.js" ); ?>
        <?php $this->registerJsFile("@web/vendors/nicescroll/jquery.nicescroll.min.js" ); ?>
        <?php $this->registerJsFile("@web/vendors/waves/waves.min.js" ); ?>
        <?php $this->registerJsFile("@web/vendors/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js" ); ?>
        <?php $this->registerJsFile("@web/js/bootstrap.min.js" ); ?>
        <?php $this->registerJsFile("@web/js/tree.jquery.js" ); ?>
        <?php $this->registerJsFile("@web/js/functions.js" ); ?>
        <?php $this->registerJsFile("@web/js/helpers.js" ); ?>
        <!-- JS -->

    <?php $this->endBody() ?>
    </body>

</html>

<?php $this->endPage() ?>
