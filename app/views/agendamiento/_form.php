<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillasJornadasCab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-cuadrillas-jornadas-cab-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_sys_empresa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_sys_rrhh_cuadrillas_jornadas_cab')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_incio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_fin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transaccion_usuario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'semana')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_sys_rrhh_cuadrillas')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
