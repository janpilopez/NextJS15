<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysAdmHistorialSueldoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-historial-sueldo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'anio') ?>

    <?= $form->field($model, 'sueldo_sectorial') ?>

    <?= $form->field($model, 'sueldo_basico') ?>

    <?= $form->field($model, 'activo') ?>

    <?= $form->field($model, 'user_created') ?>

    <?php // echo $form->field($model, 'date_created') ?>

    <?php // echo $form->field($model, 'user_autorization') ?>

    <?php // echo $form->field($model, 'date_autorization') ?>

    <?php // echo $form->field($model, 'autorizado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
