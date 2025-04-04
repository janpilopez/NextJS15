<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhComedorVisitasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-comedor-visitas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'tipo_visita') ?>

    <?= $form->field($model, 'desayuno') ?>

    <?= $form->field($model, 'almuerzo') ?>

    <?= $form->field($model, 'merienda') ?>

    <?= $form->field($model, 'id_sys_rrhh_comedor_visita') ?>

    <?php // echo $form->field($model, 'codigo') ?>

    <?php // echo $form->field($model, 'id_sys_adm_departamento') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
