<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TblPatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tbl Patients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-patient-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Patient', ['create'], ['class' => 'btn btn-success','style' => 'text-aligm center']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'first_name',
            'last_name',
            'id_card',
            'brith_day',
            //'phone_number',
            //'created_at',
            //'line_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
