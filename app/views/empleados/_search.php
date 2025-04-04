<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhEmpleadosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-empleados-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'tipo_identificacion') ?>

    <?= $form->field($model, 'nombres') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'id_sys_adm_cargo') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'fecha_nacimiento') ?>

    <?php // echo $form->field($model, 'estado_civil') ?>

    <?php // echo $form->field($model, 'genero') ?>

    <?php // echo $form->field($model, 'telefono') ?>

    <?php // echo $form->field($model, 'celular') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'id_sys_parroquia') ?>

    <?php // echo $form->field($model, 'direccion') ?>

    <?php // echo $form->field($model, 'formacion_academica') ?>

    <?php // echo $form->field($model, 'titulo_academico') ?>

    <?php // echo $form->field($model, 'calificacion_positiva') ?>

    <?php // echo $form->field($model, 'discapacidad') ?>

    <?php // echo $form->field($model, 'tipo_discapacidad') ?>

    <?php // echo $form->field($model, 'por_discapacidad') ?>

    <?php // echo $form->field($model, 'ide_discapacidad') ?>

    <?php // echo $form->field($model, 'tipo_sangre') ?>

    <?php // echo $form->field($model, 'id_sys_rrhh_contrato') ?>

    <?php // echo $form->field($model, 'id_sys_adm_actividad') ?>

    <?php // echo $form->field($model, 'id_sys_adm_ccosto') ?>

    <?php // echo $form->field($model, 'tipo_empleado') ?>

    <?php // echo $form->field($model, 'lunch') ?>

    <?php // echo $form->field($model, 'valor_lunch') ?>

    <?php // echo $form->field($model, 'desayuno') ?>

    <?php // echo $form->field($model, 'almuerzo') ?>

    <?php // echo $form->field($model, 'merienda') ?>

    <?php // echo $form->field($model, 'transporte') ?>

    <?php // echo $form->field($model, 'valor_transporte') ?>

    <?php // echo $form->field($model, 'id_sys_adm_ruta') ?>

    <?php // echo $form->field($model, 'decimo') ?>

    <?php // echo $form->field($model, 'freserva') ?>

    <?php // echo $form->field($model, 'id_sys_rrhh_banco') ?>

    <?php // echo $form->field($model, 'id_sys_rrhh_forma_pago') ?>

    <?php // echo $form->field($model, 'cta_banco') ?>

    <?php // echo $form->field($model, 'num_tar') ?>

    <?php // echo $form->field($model, 'transaccion_usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
