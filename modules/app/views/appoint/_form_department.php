<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

$this->title = "นัดหมายแพทย์";
?>

<style>
    /* .login-form .btn {
        padding: 0.375rem 0.75rem;
    } */
    .list-group-item.active {
        background-color: #ff518a;
        border-color: #ff518a;
    }

    .btn-outline-secondary:not(:disabled):not(.disabled):active,
    .btn-outline-secondary:not(:disabled):not(.disabled).active,
    .show>.btn-outline-secondary.dropdown-toggle {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn {
        text-align: left;
        font-size: 16pt;
    }

    img {
        max-width: 10%;

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
                                     รายชื่อแผนก
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-department', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body">
                            <div class="form-group">
                                <!-- <div class="input-group">
                                    <input type="text" class="form-control" placeholder="ค้นหาแผนก..." aria-label="Search for...">
                                        <span class="input-group-btn">  
                                            <button class="btn btn-outline-success" type="button">ค้นหา</button>
                                        </span>
                                </div> -->

                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <input type="text" class="form-control" placeholder="ค้นหาแผนก..." aria-label="Search for...">
                                </div>
                            </div>

                            <div class="list-group">
                                <div class="input-group">
                                    <a class="list-group-item list-group-item-action active" style="font-size: 14pt;">
                                        <i class="fa fa-bullhorn"></i>
                                        เลือกแผนกที่ท่านต้องการนัดแพทย์
                                    </a>
                                </div>
                                <br>
                                <?php foreach ($DeptGroups as $key => $value) : ?>
                                    <div class="input-group">
                                        <a href="<?= Url::to(['/app/appoint/create-sub-department', 'id' => $value['DeptGroup']]) ?>" class="list-group-item btn btn-outline-success">
                                            <img src="<?= Yii::getAlias('@web/images/doctor.png') ?>" class="img-responsive" style="display: inline-block;">
                                            <?= $value['DeptGrDesc'] ?>
                                            <span class="icon-input">
                                            </span>
                                        </a>
                                    </div>
                                <?php endforeach; ?>


                            </div>
                        </div>

                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>

</div>