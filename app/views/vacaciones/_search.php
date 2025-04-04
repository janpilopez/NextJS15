<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysRrhhEmpleadosPeriodoVacacionesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-periodo-vacaciones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_empleados_periodo_vacaciones') ?>

    <?= $form->field($model, 'dias_disponibles') ?>

    <?= $form->field($model, 'dias_otorgados') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'id_sys_adm_periodo_vacaciones') ?>

    <?php // echo $form->field($model, 'dias_laborados') ?>

    <?php // echo $form->field($model, 'valor') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
