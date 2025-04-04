<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysMedConsultaMedicaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-consulta-medica-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numero') ?>

    <?= $form->field($model, 'id_sys_med_turno_medico') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'pulso') ?>

    <?php // echo $form->field($model, 'temperatura') ?>

    <?php // echo $form->field($model, 'respiracion') ?>

    <?php // echo $form->field($model, 'pa_max') ?>

    <?php // echo $form->field($model, 'pa_min') ?>

    <?php // echo $form->field($model, 'nota_enfermera') ?>

    <?php // echo $form->field($model, 'fecha_toma_datos') ?>

    <?php // echo $form->field($model, 'usuario_enfermeria') ?>

    <?php // echo $form->field($model, 'fecha_consulta') ?>

    <?php // echo $form->field($model, 'notas_evolucion') ?>

    <?php // echo $form->field($model, 'id_sys_med_cie10') ?>

    <?php // echo $form->field($model, 'prescripcion') ?>

    <?php // echo $form->field($model, 'usuario_medico') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
