<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Ver Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cedula, 'url' => ['view', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Ver Empleados';
?>
<div class="sys-rrhh-empleados-update">

    <?= $this->render('_formbloqueado', [
        'model' => $model,
        'nucleofamiliar'=> $nucleofamiliar,
        'horarios'=> $horarios,
        'haberes'=> $haberes,
        'sueldos'=> $sueldos,
        'contratos'=> $contratos,
        'cargos'=> $cargos,
        'gastos'=> $gastos,
        'documentos' => $documentos,
        'fotos'=> $fotos,
        'update'=>1 ,
    ]) ?>

</div>
