<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysRrhhCuadrillasJornadasMovSearch*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-cuadrillas-jornadas-cab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'id_sys_rrhh_cuadrillas_jornadas_cab') ?>

    <?= $form->field($model, 'fecha_incio') ?>

    <?= $form->field($model, 'fecha_fin') ?>

    <?= $form->field($model, 'transaccion_usuario') ?>

    <?php // echo $form->field($model, 'semana') ?>

    <?php // echo $form->field($model, 'id_sys_rrhh_cuadrillas') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
