<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysDocumentoAutorizacionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-documento-autorizacion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sys_documento') ?>

    <?= $form->field($model, 'id_usuario') ?>

    <?= $form->field($model, 'id_sys_area') ?>

    <?= $form->field($model, 'id_sys_departamento') ?>

    <?php // echo $form->field($model, 'usuario_transaccion') ?>

    <?php // echo $form->field($model, 'fecha_transaccion') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
