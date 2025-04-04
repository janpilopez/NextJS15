<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpresaServiciosEmpleados*/

$this->title = 'Actualizar Empleado';
$this->params['breadcrumbs'][] = ['label' => 'Empresa Servicios Empleados', 'url' => ['index']];
?>
<div class="sys-rrhh-empresa-servicios-empleados-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
