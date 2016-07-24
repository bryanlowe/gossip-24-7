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
    public $css = [
        '/css/flexslider.css',
        '/css/bootstrap.css',
        '/css/popuo-box.css',
        '/css/font-awesome.min.css',
        '/css/style.css'
    ];
    public $js = [
        '/js/jquery.min.js',
        '/js/jquery.flexisel.js',
        '/js/jquery.flexslider.js',
        '/js/jquery.leanModal.min.js',
        '/js/jquery.magnific-popup.js',
        '/js/responsiveslides.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
