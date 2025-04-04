<?php

use app\models\SysSsooRegistroEntregaDetalle;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Agregar Epp a Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Crear Registro a Empleado', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-adm-areas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelDetalle' => [new SysSsooRegistroEntregaDetalle()],
        'listaActividades' => $listaActividades
    ]) ?>

</div>
