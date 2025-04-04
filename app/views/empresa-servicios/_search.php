<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhEmpresaServicios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empresa-servicios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_empresa_servicio') ?>

    <?= $form->field($model, 'ruc') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
