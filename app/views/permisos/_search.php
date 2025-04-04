<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhEmpleadosPermisosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-permisos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_empleados_permiso') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'id_sys_rrhh_permiso') ?>

    <?= $form->field($model, 'fecha_ini') ?>

    <?= $form->field($model, 'fecha_fin') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'comentario') ?>

    <?php // echo $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'hora_ini') ?>

    <?php // echo $form->field($model, 'hora_fin') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
