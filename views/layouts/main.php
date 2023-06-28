<?php
    use yii\helpers\Html;
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\widgets\Breadcrumbs;
    use app\assets\AppAsset;

    /* @var $this \yii\web\View */
    /* @var $content string */

    AppAsset::register( $this );
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode( $this->title ) ?></title>
    <?php $this->head() ?>
</head>
<body class="toggled sw-toggled">
<?php $this->beginBody() ?>
<header id="header">
    <ul class="header-inner">
        <li data-trigger="#sidebar" id="menu-trigger">
            <div class="line-wrap">
                <div class="line top"></div>
                <div class="line center"></div>
                <div class="line bottom"></div>
            </div>
        </li>
        <li class="logo hidden-xs">
            <?= Html::a( Html::img( '@web/img/tz-logotype.svg' ), [ '/app-case' ] ) ?>
        </li>
        <li class="pull-right">
            <ul class="top-menu">
                <li class="fullname"><?= Html::a(Yii::$app->session->get('user.full_name'), [ '/user/profile?id='.Yii::$app->session->get('user.id') ], ['class' => ' user_name']) ?></li>
<!--                <li class="dropdown">-->
<!--                    <a data-toggle="dropdown" class="tm-notification" href="">-->
<!--                        <i class="tmn-counts">9</i>-->
<!--                    </a>-->
<!--                    <div class="dropdown-menu dropdown-menu-lg pull-right">-->
<!--                        <div class="listview" id="notifications">-->
<!--                            <div class="lv-header">No newsflashes</div>-->
<!--                            <div class="lv-body c-overflow" tabindex="2" style="overflow: hidden; outline: none;"></div>-->
<!--                            <a class="lv-footer" href="">View Previous</a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
                <li class="dropdown">
                    <a href="" class="tm-settings" data-toggle="dropdown"></a>
                    <ul class="dropdown-menu dm-icon pull-right">
                        <li><?= Html::a( '<i class="md md-person"></i> Profile', [ '/user/profile?id='.Yii::$app->session->get('user.id') ] ) ?></li>
                        <li><?= Html::a( '<i class="md md-history"></i> Logout', [ '/userlogin/logout' ] ) ?></li>
                    </ul>
                </li>
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
    <aside id="sidebar">
        <div class="sidebar-inner">
            <div class="si-inner">
                <ul class="main-menu">
                    <li><?= Html::a( '<i class="md md-dashboard"></i> Dashboard', [ '/dashboard' ],[ 'class' => 'nav-bar-item nav-bar-tooltip','title'=> 'Dashboard Details Generate & Download Issues Report' ]  ) ?></li>
                        <li class="sub-menu" data-menu="1" title="All Issues">
                            <?= Html::a( '<i class="md md-insert-drive-file"></i> Issues', [ '/app-case' ] ) ?>
                            <ul>
                                <li><?= Html::a( 'All Issues', [ '/app-case' ], [ 'class' => 'nav-bar-item' ]  ) ?></li>
                                <li><?= Html::a( 'My Issues', [ '/app-case/my-issues?page=1&sort=-updated' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                <li><?= Html::a( 'Recent Issues', [ '/app-case/recent-issues?page=1&sort=-updated' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                <li><?= Html::a( 'Other Associated Account Issues', [ '/app-case/other-account-issues?page=1&sort=-updated' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                            </ul>
                        </li>
                    <?php if( Yii::$app->session->get('user.role_id') != ROLE_CLIENT_MANAGER ): ?>
                        <?php
//                        if( Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_SAFETY_CONTRACTOR && Yii::$app->session->get('user.role_id') != ROLE_CLIENT_SAFETY_PERSONNEL):
                        if( Yii::$app->session->get('user.role_id') == ROLE_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_TRADE_PARTNER):
                            ?>
                            <li class="sub-menu" data-menu="2" title="Add Jobsite's Logistics">
                                <?php
//                        if( Yii::$app->session->get('user.role_id') != ROLE_WT_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_WT_SAFETY_PERSONNEL && Yii::$app->session->get('user.role_id') != ROLE_SAFETY_CONTRACTOR && Yii::$app->session->get('user.role_id') != ROLE_CLIENT_SAFETY_PERSONNEL):
                        if( Yii::$app->session->get('user.role_id') != ROLE_TRADE_PARTNER):
                            ?>
                                <?= Html::a( '<i class="md md-place"></i> Jobsites', [ '/jobsite' ] ) ?>
                                <ul>
                                    <?php if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN ): ?>
                                    <li><?= Html::a( 'Jobsites', [ '/jobsite' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <?php endif; ?>
                                     <li><?= Html::a( 'Buildings', [ '/building' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                      <li><?= Html::a( 'Floors', [ '/floor' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <li><?= Html::a( 'Areas', [ '/area' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                <li><?= Html::a( 'Sub jobsites', [ '/sub-jobsite' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <li class="sub-menu" data-menu="3" title="Add Contractors and Users ">
                                <?= Html::a( '<i class="md md-people"></i> Users', [ '/users' ] ) ?>
                                <ul>
                                    <li>
                                       <?php if( Yii::$app->session->get('user.role_id') != ROLE_TRADE_PARTNER):
                            ?>
                                            <?= Html::a( 'Contractors', [ '/contractor' ], [ 'class' => 'nav-bar-item' ] ) ?>
                                        <?php endif; ?>
                                    </li>
                                    <li><?= Html::a( 'Users', [ '/user' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                     <?php if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN ): ?>
                                    <li><?= Html::a( 'Merge Users', [ '/user/merge-user' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <?php if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN ): ?>
                            <li class="sub-menu" data-menu="4" title="Setup Details">
                                <?= Html::a( '<i class="md md-settings"></i> Setup', [ '/app-case' ] ) ?>
                                <ul>
                                    <li><?= Html::a( 'Body Parts', [ '/body-part' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <li><?= Html::a( 'Injury Types', [ '/injury-type' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <li><?= Html::a( 'Safety Codes', [ '/app-case-sf-code' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <li><?= Html::a( 'Report Topics', [ '/report-topic' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                    <li><?= Html::a( 'Trades', [ '/trade' ] ) ?></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN ): ?>
                            <li class="sub-menu" data-menu="4" title="Upload Data">
                                <?= Html::a( '<i class="md md-attach-file"></i> Import', [ '#' ] ) ?>
                                <ul>
                                    <li><?= Html::a( 'Jobsites', [ '/import/jobsite' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                   <!--  <li><?= Html::a( 'Contractors', [ '/import/contractor' ], [ 'class' => 'nav-bar-item' ] ) ?></li> -->
                                    <li><?= Html::a( 'Users', [ '/import/user' ], [ 'class' => 'nav-bar-item' ] ) ?></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if( Yii::$app->session->get('user.role_id') == ROLE_SYSTEM_ADMIN || Yii::$app->session->get('user.role_id') == ROLE_ADMIN ): ?>
                            <li>
                                <?= Html::a( '<i class="md md-view-quilt"></i> GenQrCode', [ '/user/gen-qr-code'],[ 'class' => 'nav-bar-item','title'=> 'New User Registration and Safety Orientation Form' ] ) ?>
                            </li>
                        <?php endif; ?>

                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div id="toggle-width" class="pull-right m-r-25">
            <div class="toggle-switch">
                <input id="tw-switch" type="checkbox" hidden="hidden">
                <label for="tw-switch" class="ts-helper"></label>
            </div>
        </div>
    </aside>

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
<?php $this->registerJsFile( "@web/js/jquery.quicksearch.js" ); ?>
<?php $this->registerJsFile( "@web/js/functions.js" ); ?>
<?php $this->registerJsFile( "@web/js/helpers.js" ); ?>
<?php $this->registerJsFile( "@web/js/jquery.cookie.js" ); ?>
<!-- JS -->

<script language="javascript" src="https://code.jquery.com/jquery-1.4.2.min.js"></script>
<script>

    $(document).ready( function()
    {
        getNewsflash(
            "<?= Yii::$app->urlManager->createUrl('ajax/get-newsflash?id=') ?>"
            ,"<?= Yii::$app->session->get('user.id') ?>"
            ,".tm-notification"
            ,".c-overflow"
            ,"<?= Yii::$app->urlManager->createUrl('ajax/mark-newsflash-read') ?>"
            ,"<?= Yii::getAlias('@web') ?>"
        );
    })

</script>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
