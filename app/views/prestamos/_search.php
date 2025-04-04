<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhPrestamosCabSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-prestamos-cab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_prestamos_cab') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'comentario') ?>

    <?= $form->field($model, 'valor') ?>

    <?php // echo $form->field($model, 'cuotas') ?>

    <?php // echo $form->field($model, 'anio_ini') ?>

    <?php // echo $form->field($model, 'mes_ini') ?>

    <?php // echo $form->field($model, 'periodo_rol') ?>

    <?php // echo $form->field($model, 'nperiodo') ?>

    <?php // echo $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
