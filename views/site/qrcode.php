<style>
#qrcode img{
    margin: auto;
}
</style>

<div class="login-content">

    <div class="login-form">
        <div class="text-center" id="qrcode">
        </div>
        <p>
            <h2 style="font-weight: bold;text-align:center">
            สแกน QR Code นี้ ณ จุดบริการตู้ Kiosk
            </h2>
        </p>

    </div>
</div>


<?php

$this->registerJsFile(
    '@web/js/qrcode.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJs(
    <<<JS
 new QRCode(document.getElementById("qrcode"), {
    text: "{$appoint->qrcode}",
    width: 300,
    height: 320,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
JS
);
?>