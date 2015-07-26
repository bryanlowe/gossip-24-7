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
        '/css/bootstrap.min.css',
        '/css/sb-admin-2.css',
        '/css/font-awesome.min.css',
        '/css/pure-min.css',
        '/css/jquery-impromptu.css'
    ];
    public $js = [
        '/js/bootstrap.min.js',
        '/js/jquery-impromptu.min.js',
        '/js/common-functions.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
