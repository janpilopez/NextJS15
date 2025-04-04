<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Registar Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-empleados-create">

   
    <?= $this->render('_form', [
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
        'update'=> $update,
    ]) ?>

</div>
