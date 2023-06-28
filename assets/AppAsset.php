<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css =
        [
            'vendors/noUiSlider/jquery.nouislider.css',
            'css/bootstrap.css',
            'css/app.css',
            'css/animate.css',
            'css/jqtree.css',
            'css/overwrite.css',
            'css/sweet-alert.min.css',
            'css/buttonAnimation.css',
            'css/spinner.css',
            'https://unpkg.com/filepond@^4/dist/filepond.css',
            'css/datatables.min.css'
        ];
    public $js = ['js/datatables.js',
    'js/dataTables.checkboxes.min.js'];
    public $depends = [
        //'yii\web\YiiAsset'
        //'yii\bootstrap\BootstrapAsset',
    ];
}
