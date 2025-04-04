<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhUtilidadesCabSearch
 * */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-utilidades-cab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'anio') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'valor_uti_empleado') ?>

    <?= $form->field($model, 'valor_uti_carga') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'valor_uti') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <?php // echo $form->field($model, 'fecha_creacion') ?>

    <?php // echo $form->field($model, 'usuario_actualizacion') ?>

    <?php // echo $form->field($model, 'fecha_actualizacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
