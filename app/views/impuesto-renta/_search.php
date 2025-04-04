<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhImpuestoRentaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-impuesto-renta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_impuesto_renta') ?>

    <?= $form->field($model, 'fraccion_basica') ?>

    <?= $form->field($model, 'fraccion_excedente') ?>

    <?= $form->field($model, 'impuesto_fraccion_basica') ?>

    <?= $form->field($model, 'impuesto_fraccion_excedente') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
