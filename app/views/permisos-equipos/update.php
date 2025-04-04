<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */

$this->title = 'Update Sys Rrhh Empleados Permisos Equipos: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Empleados Permisos Equipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sys-rrhh-empleados-permisos-equipos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet
    ]) ?>

</div>
