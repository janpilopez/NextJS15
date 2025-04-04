<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisos */

$this->title = 'Actualizar Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Empleados Permisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-empleados-permisos-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'listpermisos'=> $listpermisos,
        'tipousuario' => $tipousuario
    ]) ?>
</div>
