<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\RegisterModel;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$assisstances = RegisterModel::getAssisstanceTypes();

$this->title = 'Registro';
?>
<div class="site-register">
    <?= Html::img('@web/images/logo.png', ['class' => 'image-responsive']) ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, complete el formulario de registro:</p>

    <?php $form = ActiveForm::begin([ 'id' => 'register-form']); ?>
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'surname') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'phone') ?>
    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'confirm')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
