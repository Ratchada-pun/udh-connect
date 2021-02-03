<?php

use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

$this->title = "ใบนัดหมายล่วงหน้า";
?>

<style>
    .card .card-header {
        padding: 1rem;
        border-bottom: 1px solid #dc3545;
        background: #f06292;
    }

    .card_chart {
        padding: 10px 24px 14px 24px;
        position: relative;
        background: #eeeeee;
    }

    .card-body {
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        min-height: 1px;
        padding: 0.50rem;
    }

    @media (max-width: 767px) {
        .quick-links-grid .ql-grid-item {
            width: 100% !important;
        }

        .login-content {
            margin: 0;
        }

        .container-fluid {
            padding-right: 0;
            padding-left: 0;

        }
    }

    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%;
        padding-top: 25px;
        height: 0;
    }

    .login-form {
        padding: 10px 30px 30px;
    }
</style>

<div class="sufee-login d-flex align-content-center flex-wrap">
    <div class="container">
        <div class="login-content">
            <div class="card-header text-white bg-danger border-0  text-center">
                <div class="media p-6">
                    <div class="media-body">
                        <p class="btn-flat m-b-30 m-t-30">
                            <strong class="">
                                <p style="font-size: 16pt;margin-top:5px;">
                                ใบนัดหมายล่วงหน้า
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-follow-up', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body">
                            <?php    /*
                            <div class="col-sm-12">
                                <h3 class="text-center no-padding no-margin" style="font-size: 16pt;">ขั้นตอนการรับบริการ</h3>
                                <div class="videoWrapper">
                                    <iframe id="player" type="text/html" src="//www.youtube.com/embed/ERcOyeUuURo" frameborder="0"></iframe>
                                </div>
                            </div>
                            */ ?>
                            <br>
                            <!-- <div class="card card-shadow ">
                                <h5 class="card-header" style="color: #ffff;text-align:center;">
                                    ใบนัดหมาย
                                </h5>
                            </div> -->
                            <div class="card-body card_chart">
                                <address>
                                    <div style="padding-top:15px;">
                                        <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                            HN :
                                        </h>
                                        <small style="font-size: 12pt; padding-left:10%">
                                            <?= empty($appoint['hn']) ?  '-' : $appoint['hn']  ?>
                                        </small>
                                    </div>

                                    <div style="padding-top:15px;">
                                        <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                            ชื่อ :
                                        </h>
                                            <small style="font-size: 12pt; padding-left:10%">
                                                <?= $appoint['firstName'] . ' ' . $appoint['lastName'] ?>
                                            </small>
                                    </div>
                                    <br>
                                    <div>
                                        <h class="text-muted" style="padding-left:5%;font-size: 12pt; " >
                                            แผนก :
                                        </h>
                                            <small style="font-size: 12pt;padding-left:6% ">
                                                <?= $appoint['deptDesc'] ?>
                                            </small>

                                    </div>
                                    <br>
                                    <div>
                                        <h class="text-muted" style="padding-left:5%; font-size: 12pt; ">
                                            แพทย์ :
                                        </h>
                                            <small style="font-size: 12pt; padding-left:6% ">
                                                <?= $appoint['docName'] . ' ' . $appoint['docLName'] ?>
                                            </small>

                                    </div>
                                    <br>
                                    <div>
                                        <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                            วันที่นัด:
                                        </h>
                                            <small style="font-size: 12pt; padding-left:5% ">
                                                <?= substr($appoint['appoint_date'], 6, 2) . '/' . substr($appoint['appoint_date'], 4, -2) . '/' . substr($appoint['appoint_date'], 0, 4) ?>
                                            </small>
                                    </div>
                                    <br>
                                    <div>
                                        <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                            เวลานัด :
                                        </h>
                                            <small style="font-size: 12pt; padding-left:4%">
                                                <?= $appoint['appoint_time_from'] ?> - <?= $appoint['appoint_time_to'] ?> น.
                                            </small>

                                    </div>
                                    <br>
                                    <?php
                                    echo ' <div class="alert alert-danger border-0" role="alert">
                                    <h2 class="text-center" style="color: red">
                                        ' . $message . '
                                    </h2>    
                                    </div>';
                                    ?>
                                  
                                </address>

                                <div class="col-sm-12">
                                    <h3 class="text-center no-padding no-margin" style="font-size: 16pt;">ขั้นตอนการรับบริการ</h3>
                                    <div class="videoWrapper">
                                        <iframe id="player" type="text/html" src="//www.youtube.com/embed/ERcOyeUuURo" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>
<?php
echo $this->render('menu');
?>