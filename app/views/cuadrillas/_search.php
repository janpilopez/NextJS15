<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysRrhhCuadrillasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-cuadrillas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_cuadrilla') ?>

    <?= $form->field($model, 'cuadrilla') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'transaccion_usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
