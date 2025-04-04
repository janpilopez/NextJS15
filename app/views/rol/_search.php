<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCabSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-rol-cab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'anio') ?>

    <?= $form->field($model, 'mes') ?>

    <?= $form->field($model, 'periodo') ?>

    <?= $form->field($model, 'fecha_registro') ?>

    <?= $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <?php // echo $form->field($model, 'fecha_ini_liq') ?>

    <?php // echo $form->field($model, 'fecha_fin_liq') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
