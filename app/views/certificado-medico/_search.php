<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysMedCertficadoMedicoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-certficado-medico-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'fecha_ini') ?>

    <?= $form->field($model, 'fecha_fin') ?>

    <?= $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'diagnostico') ?>

    <?php // echo $form->field($model, 'entidad_emisora') ?>

    <?php // echo $form->field($model, 'usuario_creacion') ?>

    <?php // echo $form->field($model, 'fecha_creacion') ?>

    <?php // echo $form->field($model, 'usuario_actualizacion') ?>

    <?php // echo $form->field($model, 'fecha_actualizacion') ?>

    <?php // echo $form->field($model, 'anulado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
