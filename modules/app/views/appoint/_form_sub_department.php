<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\assets\SweetAlert2Asset;


/* @var $this yii\web\View */
/* @var $model app\models\TblPatient */
/* @var $form yii\widgets\ActiveForm */

$this->title = "นัดหมาย";
SweetAlert2Asset::register($this);
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

    .sufee-login img {
    max-width: 10%;
    }

    .btn {
        text-align: left;
        font-size: 16pt;
    }

    .img {
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
                                    นัดหมายล่วงหน้า
                                </p>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="login-form">
                <?php $form = ActiveForm::begin(['id' => 'form-sub-department', 'type' => ActiveForm::TYPE_VERTICAL, 'options' => ['data-pjax' => true]]); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body">
                            <div class="form-group">
                              <!-- <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <input type="text" class="form-control" placeholder="ค้นหาแผนก..." >
                                </div> -->
                                <div class="input-group">
                                    <a class="list-group-item list-group-item-action active" style="font-size: 16pt;">
                                    แผนก : <?= $DeptGrDesc['DeptGrDesc'] ?>
                                    </a>
                                </div>
                          
                            </div>

                            <div id="list-group" class="list-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <input type="text" class="form-control" placeholder="ค้นหาแผนก..."  id="myInput" autofocus autocomplete="off">
                                </div>
                                <br>
                              
                                <?php /*
                                <?php foreach ($deptCodeSub as $key => $value) : ?>
                                    <div class="input-group">
                                        <a href="<?= Url::to(['/app/appoint/create-appointments', 'id' => $value['deptCode']]) ?>" class="list-group-item btn btn-outline-success list-group-dept">
                                            <img src="<?= ArrayHelper::getValue($images, $value['deptCode']) ?>" class="img-responsive" style="display: inline-block;">
                                            <?= $value['deptDesc'] ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                           */ ?>
                                <?php foreach ($deptCodeSub as $key => $value) : ?>
                                    <div class="input-group">
                                        <a href="<?= Url::to(['/app/appoint/create-appointments', 'id' => $value['deptCode']]) ?>" data-key="<?=$value['deptCode']?>" data-service-id="<?=$value['service_id']?>" class="list-group-item btn btn-outline-success list-group-dept">
                                         <?php /*
                                            <img src="<?= ArrayHelper::getValue($images, $value['deptCode']) ?>" class="img-responsive" style="display: inline-block;">
                                          */?>  
                                            <?= $value['deptDesc'] ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                           
                            </div>
                            <br>
                            <div>
                                <p>
                                    <a href="/app/appoint/create-department" class="btn btn-secondary btn-lg btn-block text-center">
                                        <i class="fa fa-reply"></i>
                                        ย้อนกลับ
                                    </a>
                                </p>
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

<?php 

$this->registerJs(
    <<<JS
// function myFunction() {
//     var input, filter, ul, li, a, i, txtValue;
//     input = document.getElementById("myInput");
//     filter = input.value.toUpperCase();
//     ul = document.getElementById("myUL");
//     li = ul.getElementsByTagName("li");
//     for (i = 0; i < li.length; i++) {
//         a = li[i].getElementsByTagName("a")[0];
//         txtValue = a.textContent || a.innerText;
//         if (txtValue.toUpperCase().indexOf(filter) > -1) {
//             li[i].style.display = "";
//         } else {
//             li[i].style.display = "none";
//         }
//     }
// }
var listDept = $('#list-group').find('a.list-group-dept')
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

$('.list-group-dept').on('click', function(e){
    e.preventDefault();
    var key = $(this).data('key')//รหัสแผนก
    $.ajax({
        method: "GET",
        url: "/app/appoint/check-schedule-doctor?dept_code=" + key,
        dataType: "json",
        beforeSend: function( jqXHR,  settings ){
            Swal.fire({
                title: 'กรุณารอสักครู่!',
                html: 'ระบบกำลังตรวจสอบข้อมูลแพทย์',
                timerProgressBar: true,
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
            })
        },
        success: function(result){
            Swal.close();
            if (result.value) {//ถ้ามีไม่ระบุแพทย์
                Swal.fire({
                    title: '',
                    text: "ต้องการระบุแพทย์หรือไม่?",
                    icon: 'question',
                    showCancelButton: true,
                    allowOutsideClick: false,
                    confirmButtonText: 'ต้องการ',
                    cancelButtonText: 'ไม่ต้องการ',
                }).then((result) => {
                    if (result.value) {
                        window.location.href = '/app/appoint/create-appointments?id='+key //ระบุแพทย์
                    }else{
                        window.location.href = '/app/appoint/appointments-undocter?id='+key //ไม่ระบุแพทย์
                    }
                })
            } else { //ระบุแพทย์
                window.location.href = '/app/appoint/create-appointments?id='+key
            }
        },
        error: function( jqXHR,  textStatus,  errorThrown){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorThrown,
                confirmButtonText: 'ตกลง'
            })
        },
    });
    
})
JS
);
?>
