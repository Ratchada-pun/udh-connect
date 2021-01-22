<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\Connect;
use dektrium\user\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
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
            <div class="login-form">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnBlur' => false,
                    'validateOnType' => false,
                    'validateOnChange' => false,
                ]) ?>
                <div class="form-group">
                    <label><?= $model->getAttributeLabel('login') ?></label>
                    <?php if ($module->debug) : ?>
                        <?= $form->field($model, 'login', [
                            'inputOptions' => [
                                'autofocus' => 'autofocus',
                                'class' => 'form-control',
                                'tabindex' => '1'
                            ]
                        ])->dropDownList(LoginForm::loginList())->label(false);
                        ?>

                    <?php else : ?>

                        <?= $form->field(
                            $model,
                            'login',
                            ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
                        )->label(false);
                        ?>

                    <?php endif ?>
                </div>
                <div class="form-group">
                    <?php if ($module->debug) : ?>
                        <div class="alert alert-warning">
                            <?= Yii::t('user', 'Password is not necessary because the module is in DEBUG mode.'); ?>
                        </div>
                    <?php else : ?>
                        <?= $form->field(
                            $model,
                            'password',
                            ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']]
                        )
                            ->passwordInput()
                            ->label(
                                Yii::t('user', 'Password')
                                    . ($module->enablePasswordRecovery ?
                                        ' (' . Html::a(
                                            Yii::t('user', 'Forgot password?'),
                                            ['/user/recovery/request'],
                                            ['tabindex' => '5']
                                        )
                                        . ')' : '')
                            ) ?>
                    <?php endif ?>
                </div>
                <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '3']) ?>
                <?= Html::submitButton(
                    Yii::t('user', 'Sign in'),
                    ['class' => 'btn btn-success btn-flat m-b-30 m-t-30', 'tabindex' => '4']
                ) ?>
                <?php ActiveForm::end(); ?>
                <?php if ($module->enableConfirmation) : ?>
                    <p class="text-center">
                        <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
                    </p>
                <?php endif ?>
                <?php if ($module->enableRegistration) : ?>
                    <p class="text-center">
                        <?= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register']) ?>
                    </p>
                <?php endif ?>
                <?= Connect::widget([
                    'baseAuthUrl' => ['/user/security/auth'],
                ]) ?>
            </div>
        </div>
    </div>
</div>
