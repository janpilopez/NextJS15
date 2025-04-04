<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysAdmMandosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-mandos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_adm_mando') ?>

    <?= $form->field($model, 'mando') ?>

    <?= $form->field($model, 'nivel') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 't_cobertura') ?>

    <?php // echo $form->field($model, 'n_entrevistas') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
