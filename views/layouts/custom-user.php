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

 <body class="toggled sw-toggled">
<?php $this->beginBody() ?>
<header id="header">
    <ul class="header-inner">
        <li id="menu-trigger">
            
        </li>
        <li class="logo hidden-xs">
            <?= Html::a( Html::img( '@web/img/tz-logotype.svg' ), [ '/app-case' ] ) ?>
        </li>
        <li class="pull-right">
            <ul class="top-menu">
             </ul>
        </li>
    </ul>

    <!-- Top Search Content -->
    <div id="top-search-wrap">
        <input type="text">
        <i id="top-search-close">×</i>
    </div>

</header>

<section id="main" class="wrap">

    <section id="content">
        <div class="container-fluid">
            <?= $content ?>
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <p class="pull-right">&copy; Whiting-Turner <?= date( 'Y' ) ?></p>
            </div>
        </footer>
    </section>

</section>

<!-- JS -->
<?php $this->registerJsFile( "@web/js/jquery-2.1.1.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/moment/moment.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/nicescroll/jquery.nicescroll.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/waves/waves.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/bootstrap-select/bootstrap-select.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/bootgrid/jquery.bootgrid.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/sweet-alert/sweet-alert.min.js" ); ?>
<?php $this->registerJsFile( "@web/vendors/noUiSlider/jquery.nouislider.all.min.js" ); ?>
<?php $this->registerJsFile( "@web/js/bootstrap.min.js" ); ?>
<?php $this->registerJsFile( "@web/js/bootstrap-growl.min.js" ); ?>
<?php $this->registerJsFile( "@web/js/tree.jquery.js" ); ?>
<?php $this->registerJsFile( "@web/js/jquery.multi-select.js" ); ?>
<?php $this->registerJsFile( "@web/js/functions.js" ); ?>
<?php $this->registerJsFile( "@web/js/helpers.js" ); ?>
<?php $this->registerJsFile( "@web/js/jquery.cookie.js" ); ?>
<!-- JS -->

<script language="javascript" src="https://code.jquery.com/jquery-1.4.2.min.js"></script>


<?php $this->endBody() ?>

</body>

</html>

<?php $this->endPage() ?>
