<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhRubrosGastosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-rubros-gastos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_rubros_gastos') ?>

    <?= $form->field($model, 'rubro') ?>

    <?= $form->field($model, 'detalle') ?>

    <?= $form->field($model, 'max_gasto') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
