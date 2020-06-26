<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

$this->title = "นัดหมายแพทย์";
$this->registerCssFile("@web/js/waitMe/waitMe.min.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);

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

    .sufee-login img {
        max-width: 10%;

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

        .card-body {
            padding: 0;
        }
    }
    .login-form .btn {
    font-size: 16px;

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
                                    นัดหมายแพทย์
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
                                <div class="input-group">
                                    <a class="list-group-item list-group-item-action active" style="font-size: 14pt;text-align:center;">
                                         รายชื่อแผนก
                                    </a>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </div>
                                <input type="text" class="form-control" placeholder="ค้นหาแผนก..." id="myInput" autofocus autocomplete="off">
                            </div>
                            <br>
                            <div class="list-group" id="list-group">
                                <?php foreach ($DeptGroups as $key => $value) : ?>
                                    <div class="input-group">
                                        <a href="<?= Url::to(['/app/appoint/create-sub-department', 'id' => $key]) ?>" class="list-group-item btn btn-outline-success list-group-department">
                                            <img src="<?= Yii::getAlias('@web/images/doctor.png') ?>" class="img-responsive" style="display: inline-block;">
                                            <?= $value ?>
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



<?php

$this->registerJs(
    <<<JS
var listDept = $('#list-group').find('a.list-group-department')
$('#myInput').on('keyup', function(){
    var filterKey = $(this).val().toLowerCase()
    $.each(listDept, function( index, value ) {
        if($(this)[0]){
            var txtValue = $(this)[0].textContent || $(this)[0].innerText;
            console.log(txtValue.toLowerCase().indexOf(value));
            if (String(txtValue).replace(/\s/g, '')
                      .toLowerCase()
                      .indexOf(filterKey) > -1) {
                $($(this)[0]).show()
            } else {
                $($(this)[0]).hide()
            }
        }
    });
    if(!filterKey){
        $(listDept).show()
    }
})
JS
);
?>

<?php
echo $this->render('menu');
?>