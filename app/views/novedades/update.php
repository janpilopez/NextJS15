<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosNovedades */

$this->title = 'Actualizar Novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedades Empleados', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_empleados_novedad, 'url' => ['view', 'id' => $model->id_sys_rrhh_empleados_novedad]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-empleados-novedades-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
