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

$this->title = "ประวัตินัดหมาย";


?>
<style>
    .login-form label {
        color: #6c757d;
    }

    /* @media (max-width: 767px) {
        .card-body {
            padding: 0;
        }
    } */

    .fa.pull-right {
        font-size: 25px;
    }

    .text-muted {
        color: #444 !important;
    }

    .btn-secondary {
        background-color: #ffeeff;
    }

    .login-form .btn {
        text-align: left;
    }

    .btn-light.active {
        background-color: #ffeeff;
    }

    .first-item button {
        background-color: #ffeeff;
        border-color: #ffeeff;
    }

    .first-item button.btn-light:hover {
        background-color: #ffeeff;
    }

    .login-content {
        margin-top: 0;
    }

    .w-30 {
        width: 30%;
    }

    .b-20 {
        width: 22%;
    }

    .h2 {
        font-size: 23px;
    }

    .card-body {
        -webkit-box-shadow: 2px 2px 10px 2px rgb(27 27 27 / 20%);
        box-shadow: 2px 2px 10px 2px rgb(27 27 27 / 20%);
    }
</style>

<ul class="nav nav-pills nav-fill nav-pills-danger  mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active show" data-toggle="tab" href="#tab-j_1">
            <p style="font-size: 16px;">
                <i class="fa fa-calendar-check-o"></i> นัดหมายที่จะมาถึง
            </p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-j_2">
            <p style="font-size: 16px;">
                <i class="fa fa-list-alt"></i> ประวัตินัดหมาย
            </p>
        </a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane active show" id="tab-j_1" role="tabpanel">
        <div class="sufee-login d-flex align-content-center flex-wrap">
            <div class="container" style="padding-right: 5px;padding-left: 5px;">
                <div class="login-content">
                    <!-- <div class="card-header text-white bg-danger border-0  text-center">
                        <div class="media p-6">
                            <div class="media-body">
                                <p class="btn-flat m-b-30 m-t-30">
                                    <strong class="">
                                        <p style="font-size: 16pt;margin-bottom:5px;">
                                            รายการนัดหมาย
                                        </p>
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div> -->

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
                                        <div class="card-body">
                                            <!-- <div class="baseline baseline-border" style="border-bottom: 1px dashed #ccc;">
                                                <div class="baseline-list baseline-border baseline-success"> -->
                                            <h2 class="mb-0 <?= $key == 0 ? 'first-item' : '' ?>">
                                                <button class="btn btn-light collapsed" type="button" data-toggle="collapse" data-target="#collapseOne<?= $key ?>" aria-expanded="false" aria-controls="collapseOne">
                                                    <!-- <span class="fa fa-angle-down pull-right" style="color: black;"> </span> -->

                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">HN :</td>
                                                                <td>
                                                                    <small style="font-size: 12px;">
                                                                        <?= empty($value['hn']) ?  '-' : $value['hn']  ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">วันที่ :</td>
                                                                <td>
                                                                    <small style="font-size: 12px;">
                                                                        <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">เวลา :</td>
                                                                <td>
                                                                    <small style="font-size: 12px;">
                                                                        <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">แผนก :</td>
                                                                <td>
                                                                    <small style="font-size: 12px;">
                                                                        <?= $value['deptDesc'] ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">แพทย์ :</td>
                                                                <td>
                                                                    <small style="font-size: 12px;">
                                                                        <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <?php /*
                                                        <div>
                                                            <h class="text-muted" style="font-size: 12pt;">
                                                                วันที่ :
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

                                                        <div style="padding-top: 5px;">
                                                            <h class="text-muted" style="font-size: 12pt;">
                                                                แผนก :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:5% ">
                                                                <?= $value['deptDesc'] ?>
                                                            </small>
                                                        </div>

                                                        <div style="padding-top: 5px;">
                                                            <h class="text-muted" style="font-size: 12pt;">
                                                                ชื่อแพทย์ :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:2% ">
                                                                <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                            </small>
                                                        </div>
                                                        */ ?>
                                                </button>
                                            </h2>
                                            <?php /*
                                                        <div id="collapseOne<?= $key ?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                                                            <h5 class="card-header" style="color:#3e3737;;text-align:center;background-color:#ffc1e3">
                                                                ใบนัดหมายล่วงหน้า
                                                            </h5>

                                                            <div class="card-body" style="padding-top:5px;background-color:#fce4ec;">

                                                                <table class="table">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="b-20">HN :</td>
                                                                            <td>
                                                                                <small style="font-size: 12pt;">
                                                                                    <?= empty($value['hn']) ?  '-' : $value['hn']  ?>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="b-20">ชื่อ :</td>
                                                                            <td>
                                                                                <small style="font-size: 12pt;">
                                                                                    <?= $value['firstName'] . ' ' . $value['lastName'] ?>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="b-20">วันที่นัด :</td>
                                                                            <td>
                                                                                <small style="font-size: 12pt;">
                                                                                    <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="b-20">เวลา :</td>
                                                                            <td>
                                                                                <small style="font-size: 12pt;">
                                                                                    <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="b-20">แผนก :</td>
                                                                            <td>
                                                                                <small small style="font-size: 12pt;">
                                                                                    <?= $value['deptDesc'] ?>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="b-20">แพทย์ :</td>
                                                                            <td>
                                                                                <small style="font-size: 12pt;">
                                                                                    <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                                                </small>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                     <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                                HN :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:10%">
                                                                <?= empty($value['hn']) ?  '-' : $value['hn']  ?>
                                                            </small>
                                                        </div>

                                                        <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                                ชื่อ :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:10%">
                                                                <?= $value['firstName'] . ' ' . $value['lastName'] ?>
                                                            </small>
                                                        </div>
                                                        <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                                วันที่นัด:
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:4% ">
                                                                <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                            </small>
                                                        </div>

                                                        <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                                                เวลานัด :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:3%">
                                                                <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                            </small>
                                                        </div>
                                                        <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%; font-size: 12pt;">
                                                                แผนก :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:4%">
                                                                <?= $value['deptDesc'] ?>
                                                            </small>
                                                        </div>
                                                        <div style="padding-top:10px;">
                                                            <h class="text-muted" style="padding-left:5%; font-size: 12pt;">
                                                                แพทย์ :
                                                            </h>
                                                            <small style="font-size: 12pt; padding-left:4%">
                                                                <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                            </small>
                                                        </div>
                                                            </div>
                                                        </div>
                                                        */ ?>
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
    </div>
    <div class="tab-pane" id="tab-j_2" role="tabpanel">
        <div class="sufee-login d-flex align-content-center flex-wrap">
            <div class="container" style="padding-right: 5px;padding-left: 5px;">
                <div class="login-content">
                    <div class="login-form">
                        <div class="row">
                            <div class="col-12">
                                <?php
                                if (empty($history2)) {
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
                                    <?php foreach ($history2 as $key => $value) : ?>
                                        <div class="card-body">
                                            <h2 class="mb-0 <?= $key == 0 ? 'first-item' : '' ?>">
                                                <button class="btn btn-light collapsed" type="button" data-toggle="collapse" data-target="#collapseOne<?= $key ?>" aria-expanded="false" aria-controls="collapseOne">
                                                    <!-- <span class="fa fa-angle-down pull-right" style="color: black;"> </span> -->

                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">HN :</td>
                                                                <td>
                                                                <small style="font-size: 12px;">
                                                                    <?= empty($value['hn']) ?  '-' : $value['hn']  ?>
                                                                </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">วันที่ :</td>
                                                                <td>
                                                                <small style="font-size: 12px;">
                                                                    <?= substr($value['appoint_date'], 6, 2) . '/' . substr($value['appoint_date'], 4, -2) . '/' . substr($value['appoint_date'], 0, 4) ?>
                                                                </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">เวลา :</td>
                                                                <td>
                                                                <small style="font-size: 12px;">
                                                                    <?= $value['appoint_time_from'] ?> - <?= $value['appoint_time_to'] ?> น.
                                                                </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;" >แผนก :</td>
                                                                <td>
                                                                <small style="font-size: 12px;">
                                                                        <?= $value['deptDesc'] ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-30" style="font-size: 12px;">แพทย์ :</td>
                                                                <td>
                                                                <small style="font-size: 12px;">
                                                                    <?= $value['docName'] . ' ' . $value['docLName'] ?>
                                                                </small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </button>
                                            </h2>
                                            
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<?php
echo $this->render('menu');
?>