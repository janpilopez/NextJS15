<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolMov */

$this->params['breadcrumbs'][] = ['label' => 'Ajustar Rol Detalle', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->anio, 'url' => ['view', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_rrhh_concepto' => $model->id_sys_rrhh_concepto, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-empleados-rol-mov-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
