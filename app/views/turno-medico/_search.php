<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-turno-medico-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'id_sys_med_tipo_motivo') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'ini_atencion') ?>

    <?php // echo $form->field($model, 'fin_atencion') ?>

    <?php // echo $form->field($model, 'comentario') ?>

    <?php // echo $form->field($model, 'medico') ?>

    <?php // echo $form->field($model, 'atendido') ?>

    <?php // echo $form->field($model, 'anulado') ?>

    <?php // echo $form->field($model, 'usuario_creacion') ?>

    <?php // echo $form->field($model, 'fecha_creacion') ?>

    <?php // echo $form->field($model, 'usuario_anulacion') ?>

    <?php // echo $form->field($model, 'fecha_anulacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
