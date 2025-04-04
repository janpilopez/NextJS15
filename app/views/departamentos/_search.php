<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysAdmDepartamentosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-departamentos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_adm_departamento') ?>

    <?= $form->field($model, 'departamento') ?>

    <?= $form->field($model, 'rango_ip_inicio') ?>

    <?= $form->field($model, 'rango_ip_fin') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'id_sys_adm_area') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'siglas') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'color_fuente') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
