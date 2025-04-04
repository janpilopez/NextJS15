<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysSsooIncidenteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-ssoo-incidente-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sys_adm_area') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'id_sys_med_consulta_medica') ?>

    <?= $form->field($model, 'secuencial') ?>

    <?php // echo $form->field($model, 'codigo') ?>

    <?php // echo $form->field($model, 'turno') ?>

    <?php // echo $form->field($model, 'fecha') ?>

    <?php // echo $form->field($model, 'lugar') ?>

    <?php // echo $form->field($model, 'puesto_trabajo') ?>

    <?php // echo $form->field($model, 'lesion_corporal') ?>

    <?php // echo $form->field($model, 'danio_maquinaria') ?>

    <?php // echo $form->field($model, 'danio_instalaciones') ?>

    <?php // echo $form->field($model, 'danio_epp') ?>

    <?php // echo $form->field($model, 'observacion') ?>

    <?php // echo $form->field($model, 'descripcion_incidente') ?>

    <?php // echo $form->field($model, 'analisis_problema') ?>

    <?php // echo $form->field($model, 'correcion') ?>

    <?php // echo $form->field($model, 'accion_preventiva') ?>

    <?php // echo $form->field($model, 'notifica_incidente_nombre') ?>

    <?php // echo $form->field($model, 'notifica_incidente_cargo') ?>

    <?php // echo $form->field($model, 'anulado') ?>

    <?php // echo $form->field($model, 'usuario_creacion') ?>

    <?php // echo $form->field($model, 'fecha_creacion') ?>

    <?php // echo $form->field($model, 'usuario_actualizacion') ?>

    <?php // echo $form->field($model, 'fecha_actualizacion') ?>

    <?php // echo $form->field($model, 'usuario_anulacion') ?>

    <?php // echo $form->field($model, 'fecha_anulacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
