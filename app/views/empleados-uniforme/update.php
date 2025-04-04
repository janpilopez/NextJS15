<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Actualizar Uniforme Empleado';
$this->params['breadcrumbs'][] = ['label' => 'Uniforme Empleado', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cedula, 'url' => ['view', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-empleados-uniforme-update">

    <?= $this->render('_form', [
        'model' => $model,
        'fotos'=> $fotos,
        'update'=>1 ,
    ]) ?>

</div>
