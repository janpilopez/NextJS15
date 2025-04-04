<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model  app\models\search\SysRrhhFeriadosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-feriados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_feriado') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'feriado') ?>

    <?= $form->field($model, 'nacional') ?>

    <?= $form->field($model, 'id_sys_provincia') ?>

    <?php // echo $form->field($model, 'id_sys_canton') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
