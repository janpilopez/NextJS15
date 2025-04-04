<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDepSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-usuarios-dep-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_adm_usuarios_dep') ?>

    <?= $form->field($model, 'id_usuario') ?>

    <?= $form->field($model, 'area') ?>

    <?= $form->field($model, 'departamento') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
