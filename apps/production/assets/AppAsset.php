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
    public $basePath = '@webroot/apps/production/assets';
    public $baseUrl = '@web/apps/production';
    public $css = [
        '/apps/production/web/css/flexslider.css',
        '/apps/production/web/css/bootstrap.css',
        '/apps/production/web/css/popuo-box.css',
        '/apps/production/web/css/style.css'
    ];
    public $js = [
        '/apps/production/web/js/jquery.min.js',
        '/apps/production/web/js/jquery.flexisel.js',
        '/apps/production/web/js/jquery.flexslider.js',
        '/apps/production/web/js/jquery.leanModal.min.js',
        '/apps/production/web/js/jquery.magnific-popup.js',
        '/apps/production/web/js/responsiveslides.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
