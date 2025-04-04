<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisos */

$this->title = 'Permisos Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Permisos Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-empleados-permisos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'listpermisos'=> $listpermisos,
        'tipousuario' => $tipousuario
    ]) ?>

</div>
