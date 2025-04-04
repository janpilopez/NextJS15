<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPeriodoVacaciones */

$this->title = 'Create Sys Rrhh Empleados Periodo Vacaciones';
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Empleados Periodo Vacaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-periodo-vacaciones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
