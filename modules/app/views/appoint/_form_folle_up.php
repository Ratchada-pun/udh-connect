<?php

use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

$this->title = "ใบนัดหมาย";
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
                                    โรงพยาบาลอุดรธานี
                                </p>
                                <p style="font-size: 16pt;margin-bottom:5px;">
                                    นัดหมายแพทย์
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
                            <div class="card card-shadow ">
                                <h5 class="card-header" style="color: #ffff;text-align:center">
                                    ใบนัดหมาย
                                </h5>
                            </div>
                            <div class="card-body card_chart">
                               
                                <address>
                                    <div>
                                       
                                        <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;" >
                                                    ชื่อ : 
                                        </h>
                                        <small  style="font-size: 12pt; padding-left:12%"   > 
                                            <?= $appoint['firstName'] . ' ' . $appoint['lastName'] ?>
                                        </small>
                                    </div>
                                    <br>
                                    <div>
                                        <h1 class="text-muted" style="padding-left:5%;font-size: 12pt; "\ >
                                                แผนก : 
                                        </h>
                                        <small style="font-size: 12pt;padding-left:6% ">
                                            <?= $appoint['deptDesc'] ?>
                                        </small>
                                        
                                    </div>
                                    <br>
                                    <div>
                                            <h1 class="text-muted" style="padding-left:5%; font-size: 12pt; " >
                                                แพทย์ : 
                                            </h>
                                        <small style="font-size: 12pt; padding-left:6% ">
                                            <?= $appoint['docName'] . ' ' . $appoint['docLName'] ?>
                                        </small>
                                      
                                    </div>
                                    <br>
                                    <div>
                                 
                                        <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                        วันที่นัด:
                                         </h>
                                        <small style="font-size: 12pt; padding-left:5% ">
                                             <?= substr($appoint['appoint_date'], 6, 2) . '/' . substr($appoint['appoint_date'], 4, -2) . '/' . substr($appoint['appoint_date'], 0, 4) ?>
                                        </small>
                                    </div>
                                    <br>
                                    <div>
                                        <h1 class="text-muted" style="padding-left:5%;font-size: 12pt;">
                                            เวลานัด :
                                         </h>
                                         <small style="font-size: 12pt; padding-left:4%">
                                            <?= $appoint['appoint_time_from'] ?> - <?= $appoint['appoint_time_to'] ?> น.
                                         </small>
                                        
                                    </div>

                                </address>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>

</div>