<?php

use kartik\form\ActiveForm;
use yii\helpers\Url;


$this->title = "นัดหมายล่วงหน้า";
?>

<style>
    .list-group-item.active {
        background-color: #ff518a;
        border-color: #ff518a;
    }

    .btn {
        display: inline-block;
    }

    .card-body {
        padding: 0;
    }

    .card .card-header {
        background: #ff80ab;
    }

    .card .card-header .card-title {
        text-align: center;
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
                                <p style="font-size: 16pt;margin-top:5px;">
                                    นัดหมาย
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'appointments-history', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">

                        <?php if (!empty($history)) : ?>
                            <div class="card  border-danger  mb-4">
                                <div class="card-header ">
                                    <div class="card-title text-white">
                                        <i class="fa fa-bullhorn"></i>
                                        นัดหมายล่าสุด
                                    </div>
                                </div>


                                <div class="card-body card_chart">
                                    <div style="padding-top:10px;">
                                        <h1 class="text-muted" style="padding-left:10%;font-size: 12pt;">
                                            ชื่อ :
                                            </h>
                                            <small style="font-size: 12pt; padding-left:10%">
                                                <?= $history['firstName'] . ' ' . $history['lastName'] ?>
                                            </small>
                                    </div>

                                    <div style="padding-top:10px;">
                                        <h1 class="text-muted" style="padding-left:10%;font-size: 12pt; " \>
                                            แผนก :
                                            </h>
                                            <small style="font-size: 12pt;padding-left:5% ">
                                                <?= $history['deptDesc'] ?>
                                            </small>
                                    </div>

                                    <div style="padding-top:10px;">
                                        <h1 class="text-muted" style="padding-left:10%; font-size: 12pt; ">
                                            แพทย์ :
                                            </h>
                                            <small style="font-size: 12pt; padding-left:5% ">
                                                <?= $history['docName'] . ' ' . $history['docLName'] ?>
                                            </small>
                                    </div>

                                    <div style="padding-top:10px;">
                                        <h1 class="text-muted" style="padding-left:10%;font-size: 12pt;">
                                            วันที่นัด:
                                            </h>
                                            <small style="font-size: 12pt; padding-left:4% ">
                                                <?= substr($history['appoint_date'], 6, 2) . '/' . substr($history['appoint_date'], 4, -2) . '/' . substr($history['appoint_date'], 0, 4) ?>
                                            </small>
                                    </div>

                                    <div style="padding-top:10px;">
                                        <h1 class="text-muted" style="padding-left:10%;font-size: 12pt;">
                                            เวลานัด :
                                            </h>
                                            <small style="font-size: 12pt; padding-left:3%">
                                                <?= $history['appoint_time_from'] ?> - <?= $history['appoint_time_to'] ?> น.
                                            </small>
                                    </div>
                                </div>

                                <br>
                            </div>
                        <?php else : ?>
                            <div class="alert alert-danger border-0" role="alert">
									ไม่พบประวัตินัดหมายของคุณ!
							</div>
                        <?php endif; ?>

                        <div class="btn-demo mb-4">
                            <?php if (!empty($history)) : ?>
                                <a href="<?= Url::to(['/app/appoint/create-appointments', 'id' => $history['pre_dept_code'], 'doc_id' => $history['doctor']]) ?>" class="btn btn-success btn-lg btn-block">
                                    <p style="font-size: 16pt;">
                                        นัดหมายจากประวัติล่าสุด
                                    </p>
                                </a>
                            <?php endif; ?>

                            <a href="<?= Url::to(['/app/appoint/create-department']) ?>" class="btn btn-pill btn-danger btn-lg">
                                <p style="font-size: 16pt;">
                                    ทำการนัดหมายใหม่
                                </p>
                            </a>

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