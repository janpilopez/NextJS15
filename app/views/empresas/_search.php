<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysEmpresaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-empresa-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'telefono') ?>

    <?= $form->field($model, 'ruc') ?>

    <?= $form->field($model, 'representante') ?>

    <?= $form->field($model, 'razon_social') ?>

    <?= $form->field($model, 'pais') ?>

    <?php // echo $form->field($model, 'logo') ?>

    <?php // echo $form->field($model, 'id_sys_empresa') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'direccion') ?>

    <?php // echo $form->field($model, 'ciudad') ?>

    <?php // echo $form->field($model, 'celular') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
