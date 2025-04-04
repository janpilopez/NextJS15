<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhEmpleadosNovedadesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-novedades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'id_sys_rrhh_empleados_novedad') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'id_sys_rrhh_concepto') ?>

    <?= $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'cantidad') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <?php // echo $form->field($model, 'comentario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
