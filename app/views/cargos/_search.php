<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysAdmCargosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-cargos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_adm_cargo') ?>

    <?= $form->field($model, 'cargo') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'reg_horas_extras') ?>

    <?= $form->field($model, 'reg_ent_salida') ?>

    <?php // echo $form->field($model, 'id_sys_adm_departamento') ?>

    <?php // echo $form->field($model, 'id_sys_adm_mando') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
