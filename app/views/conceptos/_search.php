<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhConceptosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-conceptos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_concepto') ?>

    <?= $form->field($model, 'concepto') ?>

    <?= $form->field($model, 'tipo') ?>

    <?= $form->field($model, 'pago') ?>

    <?= $form->field($model, 'imprime') ?>

    <?php // echo $form->field($model, 'orden') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'agrupa') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'aporta_iess') ?>

    <?php // echo $form->field($model, 'aporta_renta') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
