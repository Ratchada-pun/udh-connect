<?php

use PHPUnit\Util\Log\JSON;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\assets\DatatablesBsAsset;


/* @var $this yii\web\View */
/* @var $searchModel app\models\TblPatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'รายชื่อผู้ป่วยที่ลงนัดหมาย';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile("@web/css/pagination.css", [
    'depends' => [\yii\bootstrap4\BootstrapAsset::className()],
]);
$this->registerCssFile("@web/css/dataTables.bootstrap4.min.css", [
    'depends' => [\yii\bootstrap4\BootstrapAsset::className()],
]);


?>


<div class="row">
    <div class=" col-12">
        <div class="card card-shadow mb-4">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa fa-list-alt"></i>
                    รายชื่อผู้ป่วยที่ลงนัดหมาย
                </div>
            </div>
            <div class="card-body">
                <?= GridView::widget([
                    'dataProvider' => $provider,
                    'tableOptions' => [
                        'id' => 'booking-id',
                        'class' => 'table table-bordered table-striped dataTable',
                        'style' => 'width:100%'
                    ],
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn'
                        ],
                        [
                            'attribute' => 'firstName',
                            'label' => 'ชื่อ-นามสกุล',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                            'value' => function ($model) {
                                return $model['firstName'] . ' ' . $model['lastName'] ;
                            },

                        ],
                        [
                            'attribute' => 'hn',
                            'label' => 'hn',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                        ],
                        [
                            'attribute' => 'appoint_date',
                            'label' => 'วันที่นัดหมาย',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                            'value' => function($model){
                                $y = substr($model['appoint_date'], 0, 4) ; //ปี
                                $m = substr($model['appoint_date'], 4, -2); //เดือน
                                $d = substr($model['appoint_date'], -2); //วัน
                                return "$d/$m/$y";
                            }
                        ],
                        [
                            'attribute' => 'appoint_time_from',
                            'label' => 'เวลา',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                            'value' => function ($model) {
                                return $model['appoint_time_from'] . ' - ' . $model['appoint_time_to'] . ' น.';
                            },
                        ],
                        [
                            'attribute' => 'docCode',
                            'label' => 'รหัสแพทย์',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                            'value' => function ($model) {
                                return $model['docCode'] ? $model['docCode'] : '-';
                            },

                        ],
                        [
                            'attribute' => 'docName',
                            'label' => 'ชื่อแพทย์',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                            'value' => function ($model) {
                                return $model['docName'] . ' ' . $model['docLName'];
                            },
                        ],
                        [
                            'attribute' => 'DeptGrDesc',
                            'label' => 'กลุ่มแผนก',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                        ],
                        [
                            'attribute' => 'deptDesc',
                            'label' => 'แผนก',
                            'format' => 'raw',
                            'headerOptions' => [
                                'style' => 'text-align:center;'
                            ],
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>


<?php
$this->registerJsFile(
    '@web/js/jquery.dataTables.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '@web/js/dataTables.bootstrap4.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJS(
    <<<JS
$(document).ready( function () {
    $('#booking-id').DataTable({
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        language: {
            url: '/i18n/Thai.json'
        }
    });
    
} );
JS
);

?>