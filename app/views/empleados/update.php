<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Actualizar Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cedula, 'url' => ['view', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-empleados-update">

    <?= $this->render('_form', [
        'model' => $model,
        'nucleofamiliar'=> $nucleofamiliar,
        'horarios'=> $horarios,
        'haberes'=> $haberes,
        'sueldos'=> $sueldos,
        'contratos'=> $contratos,
        'cargos'=> $cargos,
        'gastos'=> $gastos,
        'fotos'=> $fotos,
        'documentos' => $documentos,
        'update'=>1 ,
    ]) ?>

</div>
