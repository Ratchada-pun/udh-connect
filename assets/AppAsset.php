<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'js/waitMe/waitMe.min.css',
        //'css/style.css'
    ];
    public $js = [
        'https://static.line-scdn.net/liff/edge/2.1/sdk.js',
        'js/liff-starter.js',
        'js/moment.min.js',
        'js/waitMe/waitMe.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@9',
        
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
