<?php

namespace udh\assets;

use yii\web\AssetBundle;

class UDHAsset extends AssetBundle
{
    public $sourcePath = '@udh/assets/dist';
    public $css = [
        '//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700',
        'css/ionicons.css',
        'css/simple-line-icons.css',
        'css/jquery.mCustomScrollbar.css',
        'css/weather-icons.min.css',
        'css/style.css',
        'css/responsive.css'
    ];

    public $js = [
        'https://static.line-scdn.net/liff/edge/2.1/sdk.js',
        'js/jquery.mCustomScrollbar.concat.min.js',
        'js/jquery.dcjqaccordion.2.7.js',
        'js/custom.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'udh\assets\FontAwesomeAsset'
    ];
}
