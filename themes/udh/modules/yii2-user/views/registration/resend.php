<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\ResendForm $model
 */

$this->title = Yii::t('user', 'Request new confirmation message');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sufee-login d-flex align-content-center flex-wrap">
    <div class="container">
        <div class="login-content">
            <div class="logo">
                <a href="#">
                    <strong class="logo_icon">
                        <img src="/images/logonew.png" alt="">
                    </strong>
                    <span class="logo-default">
                        <img src="/images/logonew.png" alt="">
                    </span>
                </a>
            </div>
            <br>
            <h4  style="text-align: center;">
                <?= Html::encode($this->title) ?>
            </h4>
            <div class="login-form">
                <?php $form = ActiveForm::begin([
                    'id' => 'resend-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>
                <div class="form-group">
                    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                </div>
                <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-success btn-flat m-b-30 m-t-30']) ?><br>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
