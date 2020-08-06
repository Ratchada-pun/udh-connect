<?php

use app\assets\SweetAlert2Asset;
use kartik\form\ActiveForm;
use yii\helpers\Html;

SweetAlert2Asset::register($this);

Yii::$app->formatter->locale = 'th-TH';
$currentday = Yii::$app->formatter->asDate('now', 'php:d');
$currentmonth = Yii::$app->formatter->asDate('now', 'php:m');
$currentYear = Yii::$app->formatter->asDate('now', 'php:Y');
$startYear = $currentYear - 100;
$last_day_str = strtotime('last day of this month', Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')));
$last_day = Yii::$app->formatter->asDate($last_day_str, 'php:d');

$this->title = "ประวัตินัดหมายแพทย์";


?>
<style>
    .login-form label {
        color: #6c757d;
    }

    @media (max-width: 767px) {
        .card-body {
            padding: 0;
        }
    }

    .fa.pull-right {
        font-size: 25px;
    }

    .text-muted {
        color: #444 !important;
    }

    .btn-secondary {
        background-color: #d7dce0;
    }

    .login-form .btn {
        text-align: left;
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
                                <!-- <p style="font-size: 16pt;margin-top:5px;">
                                    โรงพยาบาลอุดรธานี
                                </p> -->
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    ประวัตินัดหมายแพทย์
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-user-history', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">
                        <?php
                        if (empty($history)) {
                            echo ' 
                                <div class="alert alert-warning border-0" role="alert">
                                    <div style="font-size: 14pt; color:red;">
                                        <i class="fa fa-exclamation-circle"></i>
                                        ไม่พบรายการนัดหมายแพทย์ ของคุณ
                                    </div>
                                    <div class="text-center">
                                        <a href="/app/appoint/create-department" class="btn btn-link" > 
                                        <i class="fa fa-hand-pointer-o"> </i>
                                            <b>เริ่มนัดหมายแพทย์</b>
                                        </a>
                                    </div>
                                </div>
                                ';
                        }
                        ?>

                        <div class="accordion" id="accordionExample">
                            <?php foreach ($history as $key => $value) : ?>
                                <div class="baseline baseline-border" style="border-bottom: 1px dashed #ccc;">
                                    <div class="baseline-list baseline-border baseline-success">

                                        <h2 class="mb-0">
                                            <button class="btn btn-light collapsed" type="button" data-toggle="collapse" data-target="#collapseOne<?= $key ?>" aria-expanded="false" aria-controls="collapseOne">
                                                <span class="fa fa-angle-down pull-right" style="color: black;"> </span>
                                                <div>
                                                    <h class="text-muted" style="font-size: 12pt;">
                                                        วันที่นัด :
                                                    </h>
                                                    <small style="font-size: 12pt; padding-left:3% ">
                                                        <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <h class="text-muted" style="font-size: 12pt;">
                                                        เวลา :
                                                    </h>
                                                    <small style="font-size: 12pt; padding-left:7%">
                                                        <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                    </small>

                                                </div>
                             
                                            </button>
                                        </h2>

                                        <div id="collapseOne<?= $key ?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                                            <h5 class="card-header" style="color:#3e3737;;text-align:center;background-color:#ffc1e3">
                                                ใบนัดหมาย
                                            </h5>

                                            <div class="card-body" style="padding-top:5px;background-color:#fce4ec;">
                                                <div style="padding-top:10px;">
                                                    <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                        ชื่อ :
                                                        </h>
                                                        <small style="font-size: 12pt; padding-left:10%">
                                                            <?= $value['firstName'] . ' ' . $value['lastName'] ?>
                                                        </small>
                                                </div>

                                                <div style="padding-top:10px;">
                                                    <h1 class="text-muted" style="padding-left:5%;font-size: 12pt; " \>
                                                        แผนก :
                                                        </h>
                                                        <small style="font-size: 12pt;padding-left:5% ">
                                                            <?= $value['deptDesc'] ?>
                                                        </small>
                                                </div>

                                                <div style="padding-top:10px;">
                                                    <h1 class="text-muted" style="padding-left:5%; font-size: 12pt; ">
                                                        แพทย์ :
                                                        </h>
                                                        <small style="font-size: 12pt; padding-left:5% ">
                                                            <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                        </small>
                                                </div>

                                                <div style="padding-top:10px;">
                                                    <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                        วันที่นัด:
                                                        </h>
                                                        <small style="font-size: 12pt; padding-left:4% ">
                                                            <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                        </small>
                                                </div>

                                                <div style="padding-top:10px;">
                                                    <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                        เวลานัด :
                                                        </h>
                                                        <small style="font-size: 12pt; padding-left:3%">
                                                            <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                        </small>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
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