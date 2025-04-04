<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhEmpresaServiciosEmpleadosSearch*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empresa-servicios-empleados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'id_sys_rrhh_empresa_servicios') ?>

    <?= $form->field($model, 'nombres') ?>

    <?= $form->field($model, 'genero') ?>

    <?php // echo $form->field($model, 'ocupacion') ?>

    <?php // echo $form->field($model, 'fecha_ingreso') ?>

    <?php // echo $form->field($model, 'fecha_salida') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'cargas_familiares') ?>

    <?php // echo $form->field($model, 'faltas') ?>

    <?php // echo $form->field($model, 'usuario_registro') ?>

    <?php // echo $form->field($model, 'fecha_registro') ?>

    <?php // echo $form->field($model, 'usuario_actualizacion') ?>

    <?php // echo $form->field($model, 'fecha_actualizacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
