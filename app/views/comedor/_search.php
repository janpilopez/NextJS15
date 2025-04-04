<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhComedoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-comedor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_comedor') ?>

    <?= $form->field($model, 'alimento') ?>

    <?= $form->field($model, 'h_desde') ?>

    <?= $form->field($model, 'h_hasta') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
